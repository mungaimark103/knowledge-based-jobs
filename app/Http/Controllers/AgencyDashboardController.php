<?php

namespace App\Http\Controllers;

use App\Models\CandidateProfile;
use App\Models\JobApplication;
use App\Models\MatchingCriterion;
use App\Models\MatchingRule;
use App\Models\Organization;
use App\Models\User;
use App\Services\MatchingEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AgencyDashboardController extends Controller
{
    public function index(Request $request, MatchingEngine $engine): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'agency_admin') {
            return redirect()->route('dashboard');
        }

        $organizations = Organization::withCount('jobPostings')
            ->latest()
            ->paginate(10, ['*'], 'org_page')
            ->withQueryString()
            ->through(fn ($org) => [
                'id' => $org->id,
                'name' => $org->name,
                'code' => $org->code,
                'org_type' => $org->org_type,
                'logo_path' => $org->logo_path,
                'is_verified' => (bool) $org->is_verified,
                'verified_at' => $org->verified_at ? \Carbon\Carbon::parse($org->verified_at)->format('M d, Y') : null,
                'job_count' => $org->job_postings_count,
                'created_at' => $org->created_at ? \Carbon\Carbon::parse($org->created_at)->format('M d, Y') : null,
            ]);

        $candidates = User::where('role', 'candidate')
            ->with('candidateProfile')
            ->latest()
            ->paginate(10, ['*'], 'cand_page')
            ->withQueryString()
            ->through(function ($c) {
                $prof = $c->candidateProfile;
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'email' => $c->email,
                    'education_level' => $prof?->education_level ?? 'Not Specified',
                    'years_experience' => $prof?->years_experience ?? 0,
                    'reliability_score' => $prof?->reliability_score ?? 80.0,
                    'skills' => $prof?->skills ?? [],
                    'is_verified' => (bool) ($prof?->is_verified ?? false),
                    'verified_at' => $prof?->verified_at ? \Carbon\Carbon::parse($prof->verified_at)->format('M d, Y') : null,
                    'resume_filename' => $prof?->resume_filename,
                    'created_at' => $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('M d, Y') : null,
                ];
            });

        $criteria = MatchingCriterion::orderBy('id')->get();
        $rules = MatchingRule::orderBy('id')->get();

        $applications = JobApplication::with(['candidate.candidateProfile', 'jobPosting.organization'])
            ->latest('applied_at')
            ->take(15)
            ->get()
            ->map(function ($app) use ($engine) {
                $job = $app->jobPosting;
                $candidate = $app->candidate;
                $eval = ($job && $candidate) ? $engine->evaluate($candidate, $job) : null;

                return [
                    'id' => $app->id,
                    'candidate_name' => $candidate?->name ?? 'Candidate',
                    'job_title' => $job?->title ?? $app->job_title_snapshot ?? 'Unavailable Position',
                    'organization_name' => $job?->organization?->name ?? $app->organization_name_snapshot ?? 'Closed Agency',
                    'status' => $app->status,
                    'applied_at' => \Carbon\Carbon::parse($app->applied_at ?? $app->created_at)->format('M d, Y'),
                    'kbs_score' => $eval ? $eval['score'] : 0,
                    'kbs_status' => $eval ? $eval['status'] : 'excluded',
                    'explanations' => $eval ? ($eval['explanations'] ?? []) : [],
                ];
            });

        $agencyStaff = User::where('role', 'agency_admin')->latest()->get()->map(function ($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'agency_sub_role' => $staff->agency_sub_role ?? 'super_admin',
                'created_at' => $staff->created_at->format('M d, Y'),
            ];
        });

        $stats = [
            'total_organizations' => Organization::count(),
            'verified_organizations' => Organization::where('is_verified', true)->count(),
            'total_candidates' => User::where('role', 'candidate')->count(),
            'verified_candidates' => CandidateProfile::where('is_verified', true)->count(),
            'total_applications' => JobApplication::count(),
            'total_staff' => $agencyStaff->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'organizations' => $organizations,
            'candidates' => $candidates,
            'agencyStaff' => $agencyStaff,
            'criteria' => $criteria,
            'rules' => $rules,
            'applications' => $applications,
            'stats' => $stats,
        ]);
    }

    public function toggleVerifyOrganization(Request $request, int $id): RedirectResponse
    {
        $org = Organization::findOrFail($id);
        $newStatus = ! $org->is_verified;

        $org->update([
            'is_verified' => $newStatus,
            'verified_at' => $newStatus ? now() : null,
            'verified_by' => $newStatus ? $request->user()->id : null,
        ]);

        $msg = $newStatus
            ? "Organization {$org->name} has been verified and awarded the KBS Official Agency Badge."
            : "Verification for {$org->name} has been revoked.";

        return redirect()->back()->with('success', $msg);
    }

    public function toggleVerifyCandidate(Request $request, int $id): RedirectResponse
    {
        $user = User::where('role', 'candidate')->findOrFail($id);
        $profile = $user->candidateProfile()->firstOrCreate(['user_id' => $user->id]);

        $newStatus = ! $profile->is_verified;

        $profile->update([
            'is_verified' => $newStatus,
            'verified_at' => $newStatus ? now() : null,
            'verified_by' => $newStatus ? $request->user()->id : null,
        ]);

        $msg = $newStatus
            ? "Candidate credentials for {$user->name} verified successfully."
            : "Verification for {$user->name} revoked.";

        return redirect()->back()->with('success', $msg);
    }

    public function createProxyCandidate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'education_level' => 'required|string',
            'years_experience' => 'required|numeric|min:0',
            'skills' => 'required|array|min:1',
            'summary' => 'nullable|string',
        ]);

        $candidate = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'),
            'role' => 'candidate',
        ]);

        CandidateProfile::create([
            'user_id' => $candidate->id,
            'education_level' => $validated['education_level'],
            'years_experience' => (int) $validated['years_experience'],
            'field_experience_months' => ((int) $validated['years_experience']) * 12,
            'skills' => $validated['skills'],
            'reliability_score' => 90.0,
            'summary' => $validated['summary'] ?? 'Assisted digital registration by Agency Staff.',
            'languages' => ['English'],
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', "Assisted candidate profile created and verified for {$candidate->name}.");
    }

    public function updateCriteriaWeights(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'criteria' => 'required|array',
            'criteria.*.id' => 'required|exists:matching_criteria,id',
            'criteria.*.weight' => 'required|numeric|min:0|max:1',
        ]);

        foreach ($validated['criteria'] as $cItem) {
            MatchingCriterion::where('id', $cItem['id'])->update(['weight' => $cItem['weight']]);
        }

        return redirect()->back()->with('success', 'KBS Matching Criteria Weights updated successfully.');
    }

    public function provisionStaff(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user || $user->role !== 'agency_admin') {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'agency_sub_role' => 'required|string|in:super_admin,verification_officer,compliance_auditor',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'agency_admin',
            'agency_sub_role' => $validated['agency_sub_role'],
        ]);

        return redirect()->back()->with('success', "Agency staff account '{$validated['name']}' provisioned successfully.");
    }

    public function exportAuditCsv(Request $request, MatchingEngine $engine)
    {
        $user = $request->user();
        if (! $user || $user->role !== 'agency_admin') {
            return redirect()->route('dashboard');
        }

        $applications = JobApplication::with(['candidate.candidateProfile', 'jobPosting.organization'])
            ->latest('applied_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="kbs_audit_trail_export_'.date('Y-m-d').'.csv"',
        ];

        $callback = function () use ($applications, $engine) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Application ID', 'Candidate Name', 'Candidate Email', 'Job Title', 'Organization', 'KBS Match Score', 'KBS Evaluation Status', 'Explanations Logged', 'Applied Date']);

            foreach ($applications as $app) {
                $job = $app->jobPosting;
                $candidate = $app->candidate;
                $eval = ($job && $candidate) ? $engine->evaluate($candidate, $job) : null;

                fputcsv($file, [
                    $app->id,
                    $candidate?->name ?? 'Candidate',
                    $candidate?->email ?? 'N/A',
                    $job?->title ?? $app->job_title_snapshot ?? 'Unavailable',
                    $job?->organization?->name ?? $app->organization_name_snapshot ?? 'Closed Agency',
                    $eval ? $eval['score'].'%' : '0%',
                    $eval ? strtoupper($eval['status']) : 'EXCLUDED',
                    implode(' | ', $eval ? ($eval['explanations'] ?? []) : []),
                    \Carbon\Carbon::parse($app->applied_at ?? $app->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user || $user->role !== 'agency_admin') {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'field' => 'required|string',
            'operator' => 'required|string|in:>=,<=,==,contains',
            'value' => 'required|string',
            'action' => 'required|string|in:flag,bonus,exclude',
            'explanation_template' => 'required|string',
        ]);

        MatchingRule::create([
            'name' => $validated['name'],
            'field' => $validated['field'],
            'operator' => $validated['operator'],
            'value' => $validated['value'],
            'action' => $validated['action'],
            'explanation_template' => $validated['explanation_template'],
            'active' => true,
        ]);

        return redirect()->back()->with('success', "Global IF-THEN Rule '{$validated['name']}' created successfully.");
    }

    public function toggleRule(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        if (! $user || $user->role !== 'agency_admin') {
            return redirect()->route('dashboard');
        }

        $rule = MatchingRule::findOrFail($id);
        $rule->update(['active' => ! $rule->active]);

        $statusStr = $rule->active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Global IF-THEN Rule '{$rule->name}' has been {$statusStr}.");
    }
}
