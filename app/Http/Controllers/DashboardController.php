<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Services\MatchingEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, MatchingEngine $engine): Response|RedirectResponse
    {
        $user = $request->user();

        // Redirect Super Admins / Agency Staff to Agency Admin Portal
        if ($user && $user->role === 'agency_admin') {
            return redirect()->route('admin.dashboard');
        }

        // Redirect Employers to Employer Portal
        if ($user && $user->role === 'employer') {
            return redirect()->route('employer.dashboard');
        }

        $user->load('candidateProfile');

        $profile = $user->candidateProfile;
        
        // Candidate has credentials if CV is uploaded OR education_level is set OR has submitted an application
        $hasCredentials = $profile && (
            ! empty($profile->resume_path) || 
            (! empty($profile->education_level) && $profile->education_level !== 'Not Specified') ||
            JobApplication::where('candidate_id', $user->id)->count() > 0
        );

        // Fetch candidate's applications
        $applications = JobApplication::with(['jobPosting.organization'])
            ->where('candidate_id', $user->id)
            ->latest('applied_at')
            ->get()
            ->map(function ($app) use ($user, $engine) {
                $job = $app->jobPosting;
                $isUnavailable = $job === null;
                $eval = ($job && $user) ? $engine->evaluate($user, $job) : null;

                return [
                    'id' => $app->id,
                    'job_id' => $job?->id,
                    'job_title' => $job?->title ?? $app->job_title_snapshot ?? 'Job No Longer Available',
                    'organization' => $job?->organization?->name ?? $app->organization_name_snapshot ?? 'Organization Account Closed',
                    'org_code' => $job?->organization?->code ?? 'N/A',
                    'grade' => $job?->grade ?? 'Closed',
                    'location' => $job?->location ?? 'N/A',
                    'status' => $isUnavailable ? 'job_unavailable' : $app->status,
                    'applied_at' => \Carbon\Carbon::parse($app->applied_at ?? $app->created_at)->format('M d, Y'),
                    'kbs_score' => $eval ? $eval['score'] : 0,
                    'kbs_status' => $isUnavailable ? 'unavailable' : ($eval ? $eval['status'] : 'excluded'),
                ];
            });

        // Recommended Jobs Feed
        $recommendedJobs = JobPosting::with('organization')->get()->map(function ($job) use ($user, $engine, $hasCredentials) {
            $eval = $hasCredentials ? $engine->evaluate($user, $job) : null;
            return [
                'id' => $job->id,
                'title' => $job->title,
                'organization' => $job->organization->name,
                'grade' => $job->grade,
                'location' => $job->location,
                'min_experience' => $job->min_experience,
                'kbs_match' => $eval ? [
                    'score' => $eval['score'],
                    'status' => $eval['status'],
                ] : null,
            ];
        })->values()->take(3);

        $stats = [
            'total_applications' => $applications->count(),
            'qualified_count' => $applications->where('kbs_status', 'recommended')->count(),
            'reliability_score' => $hasCredentials ? ($profile->reliability_score ?? 80.0) : null,
            'years_experience' => $hasCredentials ? ($profile->years_experience ?? 0) : 0,
        ];

        return Inertia::render('Dashboard', [
            'user' => $user,
            'profile' => $profile,
            'hasCredentials' => $hasCredentials,
            'stats' => $stats,
            'applications' => $applications,
            'recommendedJobs' => $recommendedJobs,
        ]);
    }
}
