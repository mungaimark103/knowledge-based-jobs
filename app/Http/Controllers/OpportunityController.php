<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationSubmittedMail;
use App\Models\CandidateProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\User;
use App\Services\MatchingEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class OpportunityController extends Controller
{
    public function index(Request $request, MatchingEngine $engine): Response
    {
        $user = $request->user();
        $hasCredentials = $user && $user->role === 'candidate' && $user->candidateProfile && ! empty($user->candidateProfile->education_level);

        $search = $request->input('search');
        $grade = $request->input('grade');
        $org = $request->input('org');

        $query = JobPosting::with('organization')->latest();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('organization', function ($oq) use ($search) {
                      $oq->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        if (! empty($grade) && $grade !== 'all') {
            $query->where('grade', $grade);
        }

        if (! empty($org) && $org !== 'all') {
            $query->whereHas('organization', function ($oq) use ($org) {
                $oq->where('code', $org);
            });
        }

        $jobPostingsPaginator = $query->paginate(10)->withQueryString();

        $jobPostingsPaginator->through(function ($job) use ($user, $engine, $hasCredentials) {
            $eval = $hasCredentials ? $engine->evaluate($user, $job) : null;
            return [
                'id' => $job->id,
                'title' => $job->title,
                'organization' => $job->organization?->name ?? 'Hiring Organization',
                'org_code' => $job->organization?->code ?? 'ORG',
                'logo_path' => $job->organization?->logo_path,
                'grade' => $job->grade,
                'location' => $job->location,
                'is_remote' => $job->is_remote,
                'description' => $job->description,
                'min_experience' => $job->min_experience,
                'required_skills' => $job->required_skills ?? [],
                'required_languages' => $job->required_languages ?? [],
                'kbs_match' => $eval ? [
                    'score' => $eval['score'],
                    'status' => $eval['status'],
                    'explanations' => $eval['explanations'],
                ] : null,
            ];
        });

        $filterGrades = JobPosting::distinct()->whereNotNull('grade')->pluck('grade')->sort()->values();
        $filterOrgs = \App\Models\Organization::whereHas('jobPostings')->select('code', 'name')->distinct()->get();

        return Inertia::render('Opportunities/Index', [
            'opportunities' => $jobPostingsPaginator,
            'filterGrades' => $filterGrades,
            'filterOrgs' => $filterOrgs,
            'filters' => [
                'search' => $search ?? '',
                'grade' => $grade ?? 'all',
                'org' => $org ?? 'all',
            ],
            'candidate' => $user ? $user->load('candidateProfile') : null,
            'hasCredentials' => $hasCredentials,
        ]);
    }

    public function show(Request $request, int $id, MatchingEngine $engine): Response
    {
        $job = JobPosting::with('organization')->findOrFail($id);
        $user = $request->user();

        $hasCredentials = $user && $user->role === 'candidate' && $user->candidateProfile && ! empty($user->candidateProfile->education_level);

        // Only evaluate if user is logged in as candidate AND has uploaded credentials
        $eval = $hasCredentials ? $engine->evaluate($user, $job) : null;

        $hasApplied = false;
        if ($user) {
            $hasApplied = JobApplication::where('job_posting_id', $job->id)
                ->where('candidate_id', $user->id)
                ->exists();
        }

        return Inertia::render('Opportunities/Show', [
            'opportunity' => [
                'id' => $job->id,
                'title' => $job->title,
                'organization' => $job->organization?->name ?? 'Hiring Organization',
                'org_code' => $job->organization?->code ?? 'ORG',
                'org_type' => $job->organization?->org_type ?? 'PRIVATE_COMPANY',
                'grade' => $job->grade,
                'location' => $job->location,
                'is_remote' => $job->is_remote,
                'description' => $job->description,
                'min_experience' => $job->min_experience,
                'required_skills' => $job->required_skills ?? [],
                'required_languages' => $job->required_languages ?? [],
            ],
            'organizationInfo' => [
                'name' => $job->organization?->name ?? 'Hiring Organization',
                'code' => $job->organization?->code ?? 'ORG',
                'type' => $job->organization?->org_type ?? 'PRIVATE_COMPANY',
                'logo' => $job->organization?->logo_path,
                'duty_station' => $job->location,
                'contract_type' => 'Fixed-Term Appointment (1 Year)',
                'mission' => $job->organization?->vision ?? 'Mandated by global treaties to deliver humanitarian response, international development, and multilateral policy advancement.',
            ],
            'evaluation' => $eval,
            'isAuthenticated' => $user !== null,
            'hasApplied' => $hasApplied,
            'candidateProfile' => $user ? $user->load('candidateProfile')->candidateProfile : null,
        ]);
    }

    /**
     * Submit 6-Step UN Inspira-Style Job Application
     */
    public function apply(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            session(['url.intended' => route('opportunities.show', ['id' => $id])]);
            return redirect()->route('login');
        }

        if ($user->role === 'employer') {
            return redirect()->back()->with('error', 'Employers cannot submit job applications. Please switch to a Job Seeker Candidate account to apply.');
        }

        $job = JobPosting::findOrFail($id);

        $validated = $request->validate([
            'screening_answers' => 'required|array|min:5',
            'screening_answers.degree' => 'required|string|in:yes',
            'screening_answers.experience' => 'required|string|in:yes',
            'screening_answers.language' => 'required|string|in:yes',
            'screening_answers.disciplinary' => 'required|string',
            'screening_answers.deployment' => 'required|string|in:yes',
            'education_data' => 'nullable|array',
            'education_history_data' => 'nullable|array',
            'work_history_data' => 'required|array',
            'references_data' => 'required|array|min:2',
            'motivational_statement' => 'required|string|min:50',
            'integrity_accepted' => 'required|accepted',
            'ai_declaration_accepted' => 'required|accepted',
        ]);

        $eduData = $validated['education_history_data'] ?? $validated['education_data'] ?? [];
        $workData = $validated['work_history_data'] ?? [];
        $refData = $validated['references_data'] ?? [];

        $application = JobApplication::updateOrCreate(
            [
                'job_posting_id' => $job->id,
                'candidate_id' => $user->id,
            ],
            [
                'status' => 'submitted',
                'job_title_snapshot' => $job->title,
                'organization_name_snapshot' => $job->organization?->name ?? 'Organization',
                'screening_answers' => $validated['screening_answers'],
                'education_data' => $eduData,
                'work_history_data' => $workData,
                'references_data' => $refData,
                'motivational_statement' => $validated['motivational_statement'],
                'integrity_accepted' => true,
                'ai_declaration_accepted' => true,
                'applied_at' => now(),
            ]
        );

        // Feed submitted application modules back into Candidate Profile Credentials database
        // Compute total years of experience accurately from submitted work positions month & year dates
        $workList = is_array($workData) && isset($workData[0]) ? $workData : [$workData];
        $computedYears = $this->calculateExperienceYears($workList);

        $profile->update([
            'education_level' => $submittedEduLevel,
            'years_experience' => max($profile->years_experience ?? 0, $computedYears),
            'field_experience_months' => max(($profile->years_experience ?? 0) * 12, $computedYears * 12),
            'education_history' => is_array($eduData) && isset($eduData[0]) ? $eduData : [$eduData],
            'work_history' => $workList,
            'references_list' => $refData,
            'reliability_score' => $profile->reliability_score ?? 88.0,
        ]);

        // Send confirmation email
        try {
            Mail::to($user->email)->send(new ApplicationSubmittedMail($application));
        } catch (\Throwable $e) {
            // Email sending failover log
        }

        return redirect()->back()->with('success', 'Your application has been successfully submitted and confirmed via email!');
    }

    /**
     * Recruiter View: List all applicants for a job posting,
     * sequenced from highest KBS suitability score to lowest.
     */
    public function applicants(Request $request, int $id, MatchingEngine $engine): Response|RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'employer') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Viewing applicant submissions requires an employer account.');
        }

        $organization = \App\Models\Organization::where('user_id', $user->id)->first();
        $job = JobPosting::with(['organization', 'applications.candidate.candidateProfile'])->findOrFail($id);

        if (! $organization || $job->organization_id !== $organization->id) {
            return redirect()->route('employer.dashboard')->with('error', 'Unauthorized access. You do not have permission to view candidate applications for this organization.');
        }

        $sequencedApplicants = $job->applications->map(function ($app) use ($job, $engine) {
            $candidate = $app->candidate;
            $eval = $engine->evaluate($candidate, $job);

            return [
                'application_id' => $app->id,
                'candidate_id' => $candidate->id,
                'name' => $candidate->name,
                'email' => $candidate->email,
                'applied_at' => \Carbon\Carbon::parse($app->applied_at ?? $app->created_at)->format('M d, Y'),
                'status' => $app->status,
                'motivational_statement' => $app->motivational_statement,
                'screening_answers' => $app->screening_answers,
                'profile' => [
                    'education_level' => $candidate->candidateProfile->education_level ?? 'N/A',
                    'years_experience' => $candidate->candidateProfile->years_experience ?? 0,
                    'skills' => $candidate->candidateProfile->skills ?? [],
                    'reliability_score' => $candidate->candidateProfile->reliability_score ?? 80,
                ],
                'kbs' => [
                    'score' => $eval['score'],
                    'status' => $eval['status'],
                    'breakdown' => $eval['breakdown'],
                    'explanations' => $eval['explanations'],
                ],
            ];
        })->sortByDesc(fn ($item) => $item['kbs']['score'])->values();

        $stats = [
            'total' => $sequencedApplicants->count(),
            'recommended' => $sequencedApplicants->where('kbs.status', 'recommended')->count(),
            'flagged' => $sequencedApplicants->where('kbs.status', 'flagged')->count(),
            'excluded' => $sequencedApplicants->where('kbs.status', 'excluded')->count(),
        ];

        return Inertia::render('Opportunities/Applicants', [
            'opportunity' => [
                'id' => $job->id,
                'title' => $job->title,
                'organization' => $job->organization->name,
                'org_code' => $job->organization->code,
                'grade' => $job->grade,
                'location' => $job->location,
                'min_experience' => $job->min_experience,
            ],
            'applicants' => $sequencedApplicants,
            'stats' => $stats,
        ]);
    }

    protected function calculateExperienceYears(array $workHistory): int
    {
        $totalMonths = 0;
        $nowYear = (int) date('Y');
        $nowMonth = (int) date('n');

        foreach ($workHistory as $w) {
            $sYear = ! empty($w['start_year']) ? (int) $w['start_year'] : null;
            $sMonth = ! empty($w['start_month']) ? (int) $w['start_month'] : 1;

            if (! $sYear) {
                continue;
            }

            $isCurrent = ! empty($w['is_current']);
            $eYear = (! $isCurrent && ! empty($w['end_year'])) ? (int) $w['end_year'] : $nowYear;
            $eMonth = (! $isCurrent && ! empty($w['end_month'])) ? (int) $w['end_month'] : $nowMonth;

            $months = (($eYear - $sYear) * 12) + ($eMonth - $sMonth) + 1;
            if ($months > 0) {
                $totalMonths += $months;
            }
        }

        return (int) max(0, round($totalMonths / 12));
    }
}
