<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\KbsRuleTemplate;
use App\Models\Organization;
use App\Services\MatchingEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EmployerDashboardController extends Controller
{
    /**
     * Handle Employer Portal Switch link for logged-in candidates or guests.
     * Logs out active candidate sessions so the user can log in / register an Employer Organization account.
     */
    public function switchPortal(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            if ($user->role === 'employer') {
                return redirect()->route('employer.dashboard');
            }

            // Candidate account clicking Employer Portal: terminate candidate session cleanly
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('register')->with('info', 'Switched to Employer Portal. Please log in or register an organization account.');
    }

    /**
     * Handle Candidate Portal Switch link when an Employer attempts to apply for a job.
     * Terminates the active employer session so the user can log in / register a Candidate account.
     */
    public function candidatePortalSwitch(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('register')->with('info', 'Switched to Job Seeker Candidate Portal. Please log in or register a candidate account to submit job applications.');
    }

    public function index(Request $request, MatchingEngine $engine): Response|RedirectResponse
    {
        $user = $request->user();

        // Security check: Only employer accounts can access the employer portal
        if (! $user || $user->role !== 'employer') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. The Employer Portal requires an employer account.');
        }

        // Find strictly the logged-in employer's organization
        $organization = Organization::where('user_id', $user->id)->first();

        // Auto-provision organization if missing for an employer user
        if (! $organization) {
            $organization = Organization::create([
                'user_id' => $user->id,
                'name' => $user->name . ' Organization',
                'code' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $user->name), 0, 6)) . rand(10, 99),
                'org_type' => 'PRIVATE_COMPANY',
                'vision' => 'Accelerating sustainable global development through automated knowledge-driven talent acquisition.',
                'about_us' => $user->name . ' is an international multilateral organization dedicated to global humanitarian and developmental outcomes.',
            ]);
        }

        $postings = JobPosting::with(['applications.candidate.candidateProfile'])
            ->where('organization_id', $organization->id)
            ->latest()
            ->get();

        $totalApplicants = 0;
        $qualifiedApplicants = 0;

        $jobsSummary = $postings->map(function ($job) use ($engine, &$totalApplicants, &$qualifiedApplicants) {
            $applicantCount = $job->applications->count();
            $totalApplicants += $applicantCount;

            $recommendedCount = 0;
            foreach ($job->applications as $app) {
                $eval = $engine->evaluate($app->candidate, $job);
                if ($eval['status'] === 'recommended') {
                    $recommendedCount++;
                    $qualifiedApplicants++;
                }
            }

            return [
                'id' => $job->id,
                'title' => $job->title,
                'grade' => $job->grade,
                'location' => $job->location,
                'min_experience' => $job->min_experience,
                'description' => $job->description,
                'required_skills' => $job->required_skills ?? [],
                'required_languages' => $job->required_languages ?? [],
                'applicant_count' => $applicantCount,
                'recommended_count' => $recommendedCount,
                'created_at' => \Carbon\Carbon::parse($job->created_at)->format('M d, Y'),
            ];
        });

        // Estimated hours saved: ~15 mins per resume manual screening
        $hoursSaved = round(($totalApplicants * 15) / 60, 1);
        $qualificationRate = $totalApplicants > 0 ? round(($qualifiedApplicants / $totalApplicants) * 100) : 0;

        $ruleTemplates = KbsRuleTemplate::where('organization_id', $organization->id)->get();

        return Inertia::render('Employer/Dashboard', [
            'organization' => $organization,
            'stats' => [
                'active_jobs' => $postings->count(),
                'total_applicants' => $totalApplicants,
                'qualification_rate' => $qualificationRate,
                'hours_saved' => $hoursSaved,
            ],
            'jobs' => $jobsSummary,
            'ruleTemplates' => $ruleTemplates,
        ]);
    }

    /**
     * Update Organization Profile (Name, Code, Org Type, Vision, About Us, Logo)
     */
    public function updateOrganization(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user || $user->role !== 'employer') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $organization = Organization::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20',
            'org_type' => 'required|string',
            'vision' => 'nullable|string',
            'about_us' => 'nullable|string',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,ico|max:10240',
            'logo_url' => 'nullable|string',
        ]);

        $logoPath = $organization->logo_path;

        try {
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $file = $request->file('logo');
                $filename = 'logo_' . $organization->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('logos', $filename, 'public');
                $logoPath = '/storage/' . $path;
            } elseif (! empty($validated['logo_url'])) {
                $logoPath = $validated['logo_url'];
            }

            $organization->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'org_type' => $validated['org_type'],
                'vision' => $validated['vision'],
                'about_us' => $validated['about_us'],
                'logo_path' => $logoPath,
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to save organization logo: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Organization details & logo updated successfully!');
    }

    /**
     * Post a New Vacancy Ad with KBS Matching Rules
     */
    public function storeJob(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user || $user->role !== 'employer') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $organization = Organization::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'grade' => 'required|string',
            'location' => 'required|string',
            'min_experience' => 'required|integer|min:0',
            'description' => 'required|string|min:20',
            'required_skills' => 'nullable|array',
            'required_languages' => 'nullable|array',
            'custom_rules' => 'nullable|array',
        ]);

        $jobPosting = JobPosting::create([
            'organization_id' => $organization->id,
            'title' => $validated['title'],
            'grade' => $validated['grade'],
            'location' => $validated['location'],
            'min_experience' => $validated['min_experience'],
            'description' => $validated['description'],
            'required_skills' => $validated['required_skills'] ?? [],
            'required_languages' => $validated['required_languages'] ?? ['English'],
            'custom_rules' => $validated['custom_rules'] ?? [],
            'is_remote' => false,
        ]);

        // Dispatch notifications to matching candidate profiles
        try {
            $reqSkills = $jobPosting->required_skills ?? [];
            $candidatesQuery = User::where('role', 'candidate');

            if (! empty($reqSkills)) {
                $candidatesQuery->whereHas('candidateProfile', function ($pq) use ($reqSkills) {
                    $pq->where(function ($sq) use ($reqSkills) {
                        foreach ($reqSkills as $s) {
                            $sq->orWhere('skills', 'like', "%{$s}%");
                        }
                    });
                });
            }

            $matchingCandidates = $candidatesQuery->take(50)->get();

            if ($matchingCandidates->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($matchingCandidates, new \App\Notifications\NewJobPostingNotification($jobPosting));
            }
        } catch (\Throwable $e) {
            // Notification failover
        }

        return redirect()->back()->with('success', "Job Vacancy '{$validated['title']}' created and matching candidates notified!");
    }
}
