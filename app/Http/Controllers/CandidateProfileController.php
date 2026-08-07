<?php

namespace App\Http\Controllers;

use App\Services\ResumeParserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateProfileController extends Controller
{
    /**
    /**
     * Upload CV/Resume File as a Reference Attachment (No brittle parsing)
     */
    public function uploadResume(Request $request): RedirectResponse
    {
        $request->validate([
            'resume' => ['required', 'file', 'max:10240', 'extensions:pdf,doc,docx,txt,rtf'],
        ], [
            'resume.required' => 'Please select a CV/Resume document to upload.',
            'resume.extensions' => 'The uploaded file must be a valid PDF, DOCX, DOC, TXT, or RTF file.',
            'resume.max' => 'The CV file size must not exceed 10MB.',
        ]);

        try {
            $user = $request->user();
            $profile = $user->candidateProfile()->firstOrCreate([
                'user_id' => $user->id,
            ]);

            $file = $request->file('resume');
            if (! $file || ! $file->isValid()) {
                return redirect()->back()->with('error', 'The uploaded file appears to be corrupted or incomplete. Please try uploading again.');
            }

            $filePath = $file->store('resumes', 'public');
            $originalName = $file->getClientOriginalName();

            $profile->update([
                'resume_path' => $filePath,
                'resume_filename' => $originalName,
            ]);

            return redirect()->back()->with('success', "CV document '{$originalName}' uploaded and attached successfully! To edit your candidate facts for KBS job matching, use the Digital CV Builder form.");
        } catch (\Throwable $e) {
            Log::error('Resume upload error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'An unexpected error occurred while attaching your CV document: ' . $e->getMessage());
        }
    }

    /**
     * View Uploaded CV Document inline in browser
     */
    public function viewResume(Request $request): StreamedResponse|RedirectResponse
    {
        $user = $request->user();
        $profile = $user->candidateProfile;

        if (! $profile || ! $profile->resume_path || ! Storage::disk('public')->exists($profile->resume_path)) {
            return redirect()->back()->with('error', 'No CV document uploaded yet.');
        }

        return Storage::disk('public')->response($profile->resume_path, $profile->resume_filename ?? 'resume.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Download Uploaded CV File
     */
    public function downloadResume(Request $request): StreamedResponse|RedirectResponse
    {
        $user = $request->user();
        $profile = $user->candidateProfile;

        if (! $profile || ! $profile->resume_path || ! Storage::disk('public')->exists($profile->resume_path)) {
            return redirect()->back()->with('error', 'No CV document uploaded yet.');
        }

        return Storage::disk('public')->download($profile->resume_path, $profile->resume_filename ?? 'resume.pdf');
    }

    /**
     * Update Structured Digital CV Profile facts directly
     */
    public function updateStructuredProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->candidateProfile()->firstOrCreate(['user_id' => $user->id]);

        $validated = $request->validate([
            'education_level' => 'required|string',
            'years_experience' => 'required|integer|min:0',
            'summary' => 'nullable|string',
            'skills' => 'required|array|min:1',
            'languages' => 'nullable|array',
            'work_history' => 'nullable|array',
            'work_history.*.role' => 'nullable|string',
            'work_history.*.employer' => 'nullable|string',
            'work_history.*.start_month' => 'nullable|string',
            'work_history.*.start_year' => 'nullable|string',
            'work_history.*.is_current' => 'nullable|boolean',
            'work_history.*.end_month' => 'nullable|string',
            'work_history.*.end_year' => 'nullable|string',
            'work_history.*.period' => 'nullable|string',
            'work_history.*.description' => 'nullable|string',
            'education_history' => 'nullable|array',
            'education_history.*.degree' => 'nullable|string',
            'education_history.*.institution' => 'nullable|string',
            'education_history.*.year' => 'nullable|string',
            'references_list' => 'nullable|array',
            'references_list.*.name' => 'nullable|string',
            'references_list.*.title' => 'nullable|string',
            'references_list.*.organization' => 'nullable|string',
            'references_list.*.email' => 'nullable|email',
            'references_list.*.phone' => ['nullable', 'string', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
        ], [
            'skills.required' => 'Please provide at least one technical or professional skill tag.',
            'references_list.*.email.email' => 'The referee email address must be a valid email format.',
            'references_list.*.phone.regex' => 'The referee phone number format is invalid (e.g. +254 700 000 000).',
        ]);

        $cleanWork = array_values(array_filter($validated['work_history'] ?? [], fn ($w) => ! empty($w['role']) || ! empty($w['employer'])));
        $computedYears = $this->calculateExperienceYears($cleanWork);
        $finalYears = max((int) $validated['years_experience'], $computedYears);

        $profile->update([
            'education_level' => $validated['education_level'],
            'years_experience' => $finalYears,
            'field_experience_months' => $finalYears * 12,
            'summary' => $validated['summary'] ?? $profile->summary,
            'skills' => $validated['skills'],
            'languages' => $validated['languages'] ?? $profile->languages,
            'work_history' => $cleanWork,
            'education_history' => array_values(array_filter($validated['education_history'] ?? [], fn ($e) => ! empty($e['degree']) || ! empty($e['institution']))),
            'references_list' => array_values(array_filter($validated['references_list'] ?? [], fn ($r) => ! empty($r['name']) || ! empty($r['organization']))),
            'reliability_score' => $profile->reliability_score ?? 85.0,
        ]);

        return redirect()->back()->with('success', 'Structured Digital CV Profile updated successfully!');
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
