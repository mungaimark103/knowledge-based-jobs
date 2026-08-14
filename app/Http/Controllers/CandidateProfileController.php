<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateProfileController extends Controller
{
    /**
     * Document type configuration mapping
     */
    protected array $docTypes = [
        'resume' => [
            'path_col' => 'resume_path',
            'name_col' => 'resume_filename',
            'folder' => 'resumes',
            'label' => 'CV / Resume',
        ],
        'recommendation_letter' => [
            'path_col' => 'recommendation_letter_path',
            'name_col' => 'recommendation_letter_filename',
            'folder' => 'recommendation_letters',
            'label' => 'Recommendation Letter',
        ],
        'references_doc' => [
            'path_col' => 'references_doc_path',
            'name_col' => 'references_doc_filename',
            'folder' => 'references_docs',
            'label' => 'References Document',
        ],
        'portfolio_doc' => [
            'path_col' => 'portfolio_doc_path',
            'name_col' => 'portfolio_doc_filename',
            'folder' => 'portfolio_docs',
            'label' => 'Work Portfolio',
        ],
        'transcripts_doc' => [
            'path_col' => 'transcripts_doc_path',
            'name_col' => 'transcripts_doc_filename',
            'folder' => 'transcripts_docs',
            'label' => 'Academic Transcripts',
        ],
    ];

    /**
     * Upload CV/Resume File (legacy compatibility route)
     */
    public function uploadResume(Request $request): RedirectResponse
    {
        $request->merge(['doc_type' => 'resume']);
        return $this->uploadDocument($request);
    }

    /**
     * Upload specific Client Document (Resume, Recommendation Letter, References, Portfolio, Academic Transcripts)
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $docType = $request->input('doc_type', 'resume');
        if (! isset($this->docTypes[$docType])) {
            $docType = 'resume';
        }

        $config = $this->docTypes[$docType];
        $fileKey = $docType === 'resume' ? 'resume' : 'document_file';

        $request->validate([
            $fileKey => ['required', 'file', 'max:15360', 'mimes:pdf,doc,docx,txt,rtf,zip,png,jpg,jpeg'],
        ], [
            "{$fileKey}.required" => "Please select a file to upload for {$config['label']}.",
            "{$fileKey}.mimes" => "The uploaded file must be a valid document (PDF, DOCX, DOC, TXT, RTF, ZIP, PNG, JPG).",
            "{$fileKey}.max" => "The file size must not exceed 15MB.",
        ]);

        try {
            $user = $request->user();
            $profile = $user->candidateProfile()->firstOrCreate(['user_id' => $user->id]);

            $file = $request->file($fileKey);
            if (! $file || ! $file->isValid()) {
                return redirect()->back()->with('error', 'The uploaded file appears to be corrupted. Please try again.');
            }

            $filePath = $file->store($config['folder'], 'public');
            $originalName = $file->getClientOriginalName();

            $profile->update([
                $config['path_col'] => $filePath,
                $config['name_col'] => $originalName,
            ]);

            AuditLogger::log(
                'DOCUMENT_UPLOAD',
                "Client uploaded {$config['label']} document '{$originalName}'.",
                ['document_type' => $docType, 'filename' => $originalName, 'file_path' => $filePath],
                $user
            );

            return redirect()->back()->with('success', "{$config['label']} '{$originalName}' uploaded successfully!");
        } catch (\Throwable $e) {
            Log::error("Document upload error ({$docType}): " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'An unexpected error occurred while uploading your document.');
        }
    }

    /**
     * View Uploaded Document inline
     */
    /**
     * View Uploaded Document inline
     */
    public function viewDocument(Request $request, string $docType = 'resume'): mixed
    {
        $user = $request->user();
        $profile = $user->candidateProfile;

        if (! isset($this->docTypes[$docType])) {
            $docType = 'resume';
        }

        $config = $this->docTypes[$docType];
        $filePath = $profile?->{$config['path_col']};
        $fileName = $profile?->{$config['name_col']} ?? "{$docType}.pdf";

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            $fileBytes = Storage::disk('public')->get($filePath);
            $mimeType = Storage::disk('public')->mimeType($filePath) ?: 'application/pdf';

            return response($fileBytes, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Cache-Control' => 'no-cache, private',
            ]);
        }

        // Clean HTML/PDF printable document preview if file is un-uploaded or on serverless
        $title = $config['label'] . ' Document - ' . ($user->name ?? 'Candidate');
        $html = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <title>{$title}</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 40px 20px; display: flex; justify-content: center; }
                .doc-card { background: #1e293b; border: 1px solid #334155; border-radius: 24px; max-width: 800px; width: 100%; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
                .header { border-b: 1px solid #334155; padding-bottom: 24px; margin-bottom: 24px; display: flex; justify-[#00b2e3]; justify-content: space-between; align-items: center; }
                .badge { background: rgba(0, 178, 227, 0.15); color: #00b2e3; border: 1px solid rgba(0, 178, 227, 0.3); font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.5px; }
                h1 { font-size: 24px; margin: 0 0 4px 0; color: #ffffff; font-weight: 800; }
                .sub { color: #94a3b8; font-size: 13px; margin: 0; }
                .section { margin-bottom: 24px; }
                .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 12px; }
                .item { background: #0f172a; border-radius: 12px; padding: 16px; margin-bottom: 12px; border: 1px solid #1e293b; }
                .item-title { font-weight: 700; font-size: 15px; color: #38bdf8; margin: 0 0 4px 0; }
                .item-meta { font-size: 12px; color: #94a3b8; margin-bottom: 8px; }
                .item-desc { font-size: 13px; color: #cbd5e1; line-height: 1.5; margin: 0; white-space: pre-line; }
                .tag { display: inline-block; background: #334155; color: #f1f5f9; font-size: 12px; padding: 4px 10px; border-radius: 8px; margin: 0 4px 6px 0; font-weight: 600; }
                .actions { margin-top: 32px; display: flex; gap: 12px; }
                .btn { background: #00b2e3; color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; font-size: 13px; cursor: pointer; text-decoration: none; }
                .btn-secondary { background: #334155; color: #f8fafc; }
            </style>
        </head>
        <body>
            <div class='doc-card'>
                <div class='header'>
                    <div>
                        <h1>{$user->name}</h1>
                        <p class='sub'>Official JobSync Digital Verified Profile Document</p>
                    </div>
                    <span class='badge'>{$config['label']} Document</span>
                </div>

                <div class='section'>
                    <div class='section-title'>Professional Summary</div>
                    <p style='font-size: 14px; color: #cbd5e1; line-height: 1.6; margin: 0;'>
                        " . e($profile?->summary ?? 'Verified professional profile registered on JobSync automated recruitment platform.') . "
                    </p>
                </div>

                <div class='section'>
                    <div class='section-title'>Education & Background</div>
                    <div class='item'>
                        <div class='item-title'>" . e($profile?->education_level ?? 'Bachelor Degree') . "</div>
                        <div class='item-meta'>" . e($profile?->years_experience ?? 0) . " Years of Verifiable Professional Experience</div>
                    </div>
                </div>

                " . ($profile?->skills ? "
                <div class='section'>
                    <div class='section-title'>Verified Core Competencies & Technical Skills</div>
                    <div>" . implode('', array_map(fn($s) => "<span class='tag'>" . e($s) . "</span>", $profile->skills)) . "</div>
                </div>" : "") . "

                " . ($profile?->work_history ? "
                <div class='section'>
                    <div class='section-title'>Work Experience History</div>
                    " . implode('', array_map(fn($w) => "
                    <div class='item'>
                        <div class='item-title'>" . e($w['role'] ?? 'Position') . " — " . e($w['employer'] ?? 'Company') . "</div>
                        <div class='item-meta'>" . e($w['start_year'] ?? '') . " - " . ($w['is_current'] ? 'Present' : e($w['end_year'] ?? '')) . "</div>
                        <p class='item-desc'>" . e($w['description'] ?? '') . "</p>
                    </div>", $profile->work_history)) . "
                </div>" : "") . "

                <div class='actions'>
                    <a href='javascript:window.print()' class='btn'>Print / Save PDF</a>
                    <a href='/dashboard' class='btn btn-secondary'>Return to Dashboard</a>
                </div>
            </div>
        </body>
        </html>";

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    /**
     * Download Uploaded Document
     */
    public function downloadDocument(Request $request, string $docType = 'resume'): mixed
    {
        $user = $request->user();
        $profile = $user->candidateProfile;

        if (! isset($this->docTypes[$docType])) {
            $docType = 'resume';
        }

        $config = $this->docTypes[$docType];
        $filePath = $profile?->{$config['path_col']};
        $fileName = $profile?->{$config['name_col']} ?? "{$docType}.pdf";

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath, $fileName);
        }

        return redirect()->back()->with('error', "No {$config['label']} document uploaded yet.");
    }

    public function viewResume(Request $request): mixed
    {
        return $this->viewDocument($request, 'resume');
    }

    public function downloadResume(Request $request): mixed
    {
        return $this->downloadDocument($request, 'resume');
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
            'references_list.*.phone' => ['nullable', 'string'],
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

        AuditLogger::log(
            'PROFILE_UPDATE',
            "Client updated Structured Profile (Education: {$validated['education_level']}, Experience: {$finalYears} yrs).",
            $validated,
            $user
        );

        return redirect()->back()->with('success', 'Client Profile updated successfully!');
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
