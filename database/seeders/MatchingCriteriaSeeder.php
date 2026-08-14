<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\CandidateProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\MatchingCriterion;
use App\Models\MatchingRule;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\NewJobPostingNotification;
use App\Services\MatchingEngine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MatchingCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Seed Agency Super Admin & Verification Staff Accounts
        $agencyAdmin = User::updateOrCreate(
            ['email' => 'admin@kbsagency.com'],
            [
                'name' => 'Agency Super Admin',
                'password' => Hash::make('password'),
                'role' => 'agency_admin',
                'agency_sub_role' => 'super_admin',
            ]
        );

        $agencyOfficer = User::updateOrCreate(
            ['email' => 'officer1@kbsagency.com'],
            [
                'name' => 'Agency Verification Officer',
                'password' => Hash::make('password'),
                'role' => 'agency_admin',
                'agency_sub_role' => 'verification_officer',
            ]
        );

        // 1. Seed Organizations across diverse sectors including Employment Agencies
        $orgTemplates = [
            [
                'name' => 'Safaricom PLC',
                'code' => 'SAFARICOM',
                'org_type' => 'PRIVATE_COMPANY',
                'is_verified' => true,
                'verified_at' => now(),
                'vision' => 'Transforming lives through technology and digital financial solutions.',
                'about_us' => 'Safaricom is Kenya\'s leading telecommunications provider and creator of M-PESA.',
                'logo_path' => 'https://upload.wikimedia.org/wikipedia/commons/e/ed/UNICEF_Logo.svg',
            ],
            [
                'name' => 'Corporate Staffing Services',
                'code' => 'CSS',
                'org_type' => 'EMPLOYMENT_AGENCY',
                'is_verified' => true,
                'verified_at' => now(),
                'vision' => 'Leading recruitment and HR consulting agency delivering executive search and staffing solutions.',
                'about_us' => 'Corporate Staffing Services is a premier HR recruitment agency in Kenya providing top talent solutions.',
                'logo_path' => null,
            ],
            [
                'name' => 'Summit Recruitment & Search',
                'code' => 'SUMMIT',
                'org_type' => 'EMPLOYMENT_AGENCY',
                'is_verified' => true,
                'verified_at' => now(),
                'vision' => 'Connecting mid to senior-level executives with top African and international employers.',
                'about_us' => 'Summit Recruitment & Search specializes in executive search, headhunting, and talent selection.',
                'logo_path' => null,
            ],
            [
                'name' => 'Flexi Personnel',
                'code' => 'FLEXI',
                'org_type' => 'EMPLOYMENT_AGENCY',
                'is_verified' => true,
                'verified_at' => now(),
                'vision' => 'East Africa\'s leading human resource outsourcing and talent management agency.',
                'about_us' => 'Flexi Personnel provides complete staffing, payroll management, and executive search services.',
                'logo_path' => null,
            ],
            [
                'name' => 'Google Technology Global',
                'code' => 'GOOGLE',
                'org_type' => 'PRIVATE_COMPANY',
                'is_verified' => true,
                'verified_at' => now(),
                'vision' => 'Organizing the world\'s information and making it universally accessible.',
                'about_us' => 'Global technology leader in search, cloud computing, AI, and software engineering.',
                'logo_path' => null,
            ],
            [
                'name' => 'World Wildlife Fund (WWF)',
                'code' => 'WWF',
                'org_type' => 'NGO',
                'vision' => 'Building a future in which humans live in harmony with nature.',
                'about_us' => 'WWF is the world\'s leading independent conservation organization.',
                'logo_path' => null,
            ],
            [
                'name' => 'Kenya Revenue Authority',
                'code' => 'KRA',
                'org_type' => 'PARASTATAL',
                'vision' => 'Facilitating global trade and revenue collection for national development.',
                'about_us' => 'KRA is the state corporation tasked with assessment, collection, and accounting of revenue.',
                'logo_path' => null,
            ],
            [
                'name' => 'UNICEF',
                'code' => 'UNICEF',
                'org_type' => 'UN_AGENCY',
                'is_verified' => true,
                'verified_at' => now(),
                'vision' => 'Advocating for child rights, survival, and protection worldwide.',
                'about_us' => 'UNICEF works in over 190 countries to reach the most disadvantaged children.',
                'logo_path' => 'https://upload.wikimedia.org/wikipedia/commons/e/ed/UNICEF_Logo.svg',
            ],
            [
                'name' => 'United Nations Development Programme',
                'code' => 'UNDP',
                'org_type' => 'UN_AGENCY',
                'is_verified' => true,
                'verified_at' => now(),
                'vision' => 'Eradicating poverty and reducing inequalities through sustainable development.',
                'about_us' => 'UNDP is the United Nations\' global development network operating in 170+ countries.',
                'logo_path' => null,
            ],
        ];

        $organizations = [];
        foreach ($orgTemplates as $ot) {
            $empUser = User::create([
                'name' => $ot['name'] . ' Recruiter',
                'email' => strtolower($ot['code']) . '@employer.com',
                'password' => Hash::make('password'),
                'role' => 'employer',
            ]);

            $ot['user_id'] = $empUser->id;
            $organizations[$ot['code']] = Organization::create($ot);
        }

        // 2. Base Matching Criteria & Standard JobSync Rules
        MatchingCriterion::updateOrCreate(['key' => 'skill_match'], ['name' => 'Skill Match', 'weight' => 0.50, 'description' => 'Percentage of required job skills matched in client profile', 'active' => true]);
        MatchingCriterion::updateOrCreate(['key' => 'experience'], ['name' => 'Experience', 'weight' => 0.30, 'description' => 'Client experience years relative to job minimum', 'active' => true]);
        MatchingCriterion::updateOrCreate(['key' => 'reliability'], ['name' => 'Reliability', 'weight' => 0.20, 'description' => 'Historical client reliability and reference score', 'active' => true]);

        MatchingRule::updateOrCreate(
            ['name' => 'Minimum Experience Requirement'],
            [
                'field' => 'years_experience',
                'operator' => '>=',
                'value' => '3',
                'action' => 'flag',
                'explanation_template' => 'Meets minimum experience threshold of :value years',
                'active' => true,
            ]
        );

        MatchingRule::updateOrCreate(
            ['name' => 'Verified Credential Priority Badge'],
            [
                'field' => 'is_verified',
                'operator' => '==',
                'value' => '1',
                'action' => 'bonus',
                'explanation_template' => 'Official JobSync Verified Client Badge awarded',
                'active' => true,
            ]
        );

        // 3. Seed Specific Safaricom PLC Positions (Entry Level & Professional set to English)
        $safaricomOrg = $organizations['SAFARICOM'];
        $safaricomJob1 = JobPosting::create([
            'organization_id' => $safaricomOrg->id,
            'title' => 'Customer Experience & Digital Support Specialist',
            'grade' => 'Entry Level',
            'location' => 'Nairobi, Kenya',
            'is_remote' => false,
            'description' => 'Safaricom PLC is seeking an ambitious Entry Level Digital Support Specialist to handle M-PESA user inquiries, digital channel support, and customer care workflows.',
            'min_experience' => 1,
            'required_skills' => ['Customer Support', 'Communication', 'M-PESA', 'Digital Troubleshooting'],
            'required_languages' => ['English'],
        ]);

        $safaricomJob2 = JobPosting::create([
            'organization_id' => $safaricomOrg->id,
            'title' => 'Senior M-PESA Backend & Cloud Engineer',
            'grade' => 'Professional',
            'location' => 'Nairobi, Kenya',
            'is_remote' => true,
            'description' => 'Professional level role leading backend architecture, microservices engineering, and distributed API scale for Safaricom financial technologies.',
            'min_experience' => 5,
            'required_skills' => ['PHP', 'Laravel', 'Microservices', 'AWS', 'SQL', 'Fintech Security'],
            'required_languages' => ['English'],
        ]);

        // Seed Employment Agencies Job Vacancies
        $cssOrg = $organizations['CSS'];
        $cssJob = JobPosting::create([
            'organization_id' => $cssOrg->id,
            'title' => 'HR & Talent Acquisition Lead (Placement)',
            'grade' => 'Professional',
            'location' => 'Nairobi, Kenya',
            'is_remote' => false,
            'description' => 'Corporate Staffing Services is managing executive recruitment for a top-tier client seeking a seasoned HR Leader.',
            'min_experience' => 6,
            'required_skills' => ['Talent Acquisition', 'HR Strategy', 'Contract Law', 'Performance Management'],
            'required_languages' => ['English'],
        ]);

        $summitOrg = $organizations['SUMMIT'];
        $summitJob = JobPosting::create([
            'organization_id' => $summitOrg->id,
            'title' => 'Senior Financial Controller (Executive Placement)',
            'grade' => 'Senior Professional',
            'location' => 'Nairobi, Kenya',
            'is_remote' => false,
            'description' => 'Summit Recruitment & Search is headhunting a Senior Financial Controller for a fast-growing multinational client.',
            'min_experience' => 8,
            'required_skills' => ['Financial Accounting', 'Auditing', 'IFRS', 'Corporate Taxation'],
            'required_languages' => ['English'],
        ]);

        $flexiOrg = $organizations['FLEXI'];
        $flexiJob = JobPosting::create([
            'organization_id' => $flexiOrg->id,
            'title' => 'IT Operations & Infrastructure Manager',
            'grade' => 'Mid Level / Associate',
            'location' => 'Nairobi, Kenya',
            'is_remote' => true,
            'description' => 'Flexi Personnel is recruiting an IT Operations Manager to oversee cloud network infrastructure and client systems.',
            'min_experience' => 4,
            'required_skills' => ['AWS', 'Docker', 'System Architecture', 'DevOps'],
            'required_languages' => ['English'],
        ]);

        // Seed additional general jobs
        $allGeneralJobs = [];
        foreach ($organizations as $code => $org) {
            if (in_array($code, ['SAFARICOM', 'CSS', 'SUMMIT', 'FLEXI'])) continue;

            $allGeneralJobs[$code] = JobPosting::create([
                'organization_id' => $org->id,
                'title' => 'Senior Officer & Operations Specialist (' . $code . ')',
                'grade' => 'Professional',
                'location' => 'Nairobi, Kenya',
                'is_remote' => rand(0, 1) === 1,
                'description' => "Key operational position driving organizational mandates for {$org->name}.",
                'min_experience' => 4,
                'required_skills' => ['Project Management', 'Data Analysis', 'Communication'],
                'required_languages' => ['English'],
            ]);
        }

        // 4. Seed Main Client (Ian Chitechi)
        $mainClientUser = User::create([
            'name' => 'Ian Chitechi',
            'email' => 'client@job-sync.com',
            'password' => Hash::make('password'),
            'role' => 'candidate',
        ]);

        CandidateProfile::create([
            'user_id' => $mainClientUser->id,
            'education_level' => 'Master\'s Degree',
            'years_experience' => 6,
            'field_experience_months' => 72,
            'reliability_score' => 92.5,
            'summary' => 'Experienced software engineer and systems specialist focused on scalable web architecture.',
            'skills' => ['PHP', 'Laravel', 'Vue.js', 'SQL', 'AWS', 'Customer Support', 'M-PESA', 'Microservices'],
            'languages' => ['English', 'Swahili'],
            'work_history' => [
                [
                    'role' => 'Senior Software Developer',
                    'employer' => 'Tech Solutions Ltd',
                    'start_year' => '2020',
                    'is_current' => true,
                    'description' => 'Architected enterprise Laravel APIs and frontend single page apps.',
                ]
            ],
            'education_history' => [
                [
                    'degree' => 'M.Sc. Computer Science',
                    'institution' => 'University of Nairobi',
                    'year' => '2020',
                ]
            ],
            'references_list' => [
                [
                    'name' => 'Dr. Andrew Omondi',
                    'title' => 'Head of Department',
                    'organization' => 'University of Nairobi',
                    'email' => 'a.omondi@uonbi.ac.ke',
                    'phone' => '+254 712 345 678',
                ]
            ],
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $agencyAdmin->id,
        ]);

        // 5. Seed 10 Additional Clients with Diverse & Distinct Profiles
        $diverseProfiles = [
            1 => [
                'name' => 'Junior Frontend Developer',
                'education' => 'Bachelor\'s Degree',
                'experience' => 2,
                'skills' => ['React', 'JavaScript', 'HTML', 'CSS', 'Tailwind'],
                'reliability' => 82.0,
                'applied_job' => $safaricomJob2,
                'status' => 'applied',
            ],
            2 => [
                'name' => 'DevOps & Cloud Engineer',
                'education' => 'Bachelor\'s Degree',
                'experience' => 5,
                'skills' => ['AWS', 'Docker', 'DevOps', 'System Architecture', 'Linux'],
                'reliability' => 94.0,
                'applied_job' => $flexiJob,
                'status' => 'hired',
            ],
            3 => [
                'name' => 'HR & Placement Lead',
                'education' => 'Master\'s Degree',
                'experience' => 7,
                'skills' => ['Talent Acquisition', 'HR Strategy', 'Contract Law', 'Performance Management'],
                'reliability' => 91.0,
                'applied_job' => $cssJob,
                'status' => 'shortlisted',
            ],
            4 => [
                'name' => 'Senior Financial Controller',
                'education' => 'Master\'s Degree',
                'experience' => 9,
                'skills' => ['Financial Accounting', 'Auditing', 'IFRS', 'Corporate Taxation'],
                'reliability' => 96.0,
                'applied_job' => $summitJob,
                'status' => 'interview',
            ],
            5 => [
                'name' => 'Customer Care & M-PESA Support Specialist',
                'education' => 'Diploma / Associate',
                'experience' => 2,
                'skills' => ['Customer Support', 'Communication', 'M-PESA', 'Digital Troubleshooting'],
                'reliability' => 89.5,
                'applied_job' => $safaricomJob1,
                'status' => 'in_review',
            ],
            6 => [
                'name' => 'Senior PHP & Fintech Engineer',
                'education' => 'Master\'s Degree',
                'experience' => 6,
                'skills' => ['PHP', 'Laravel', 'Microservices', 'AWS', 'SQL', 'Fintech Security'],
                'reliability' => 95.0,
                'applied_job' => $safaricomJob2,
                'status' => 'shortlisted',
            ],
            7 => [
                'name' => 'IT Infrastructure Specialist',
                'education' => 'Bachelor\'s Degree',
                'experience' => 4,
                'skills' => ['AWS', 'Docker', 'System Architecture', 'DevOps', 'Cybersecurity'],
                'reliability' => 86.0,
                'applied_job' => $flexiJob,
                'status' => 'in_review',
            ],
            8 => [
                'name' => 'Data Scientist & Business Analyst',
                'education' => 'Master\'s Degree',
                'experience' => 3,
                'skills' => ['Data Analysis', 'Python', 'SQL', 'Project Management', 'Communication'],
                'reliability' => 88.5,
                'applied_job' => $allGeneralJobs['GOOGLE'] ?? $safaricomJob1,
                'status' => 'shortlisted',
            ],
            9 => [
                'name' => 'Agile Project Manager',
                'education' => 'Bachelor\'s Degree',
                'experience' => 6,
                'skills' => ['Project Management', 'Data Analysis', 'Communication', 'Agile'],
                'reliability' => 90.0,
                'applied_job' => $allGeneralJobs['UNDP'] ?? $safaricomJob1,
                'status' => 'interview',
            ],
            10 => [
                'name' => 'Executive Director & Strategy Consultant',
                'education' => 'Doctorate (Ph.D. / M.D.)',
                'experience' => 12,
                'skills' => ['HR Strategy', 'Financial Accounting', 'Project Management', 'Communication'],
                'reliability' => 98.0,
                'applied_job' => $summitJob,
                'status' => 'offered',
            ],
        ];

        $matchingEngine = new MatchingEngine();

        // Also evaluate main client Ian Chitechi for Safaricom Senior Backend Engineer
        $ianEval = $matchingEngine->evaluate($mainClientUser, $safaricomJob2);
        JobApplication::create([
            'candidate_id' => $mainClientUser->id,
            'job_posting_id' => $safaricomJob2->id,
            'status' => 'shortlisted',
            'score' => $ianEval['score'],
            'kbs_status' => $ianEval['status'],
            'kbs_evaluation' => $ianEval,
            'applied_at' => now()->subDays(2),
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $pData = $diverseProfiles[$i];

            $u = User::create([
                'name' => "Client User {$i} ({$pData['name']})",
                'email' => "client{$i}@job-sync.com",
                'password' => Hash::make('password'),
                'role' => 'candidate',
            ]);

            $cp = CandidateProfile::create([
                'user_id' => $u->id,
                'education_level' => $pData['education'],
                'years_experience' => $pData['experience'],
                'skills' => $pData['skills'],
                'languages' => ['English'],
                'reliability_score' => $pData['reliability'],
                'summary' => "Professional profile for {$pData['name']} specializing in " . implode(', ', $pData['skills']) . '.',
            ]);

            // Create initial application and compute match score
            $jobToApply = $pData['applied_job'];
            $eval = $matchingEngine->evaluate($u, $jobToApply);

            JobApplication::create([
                'candidate_id' => $u->id,
                'job_posting_id' => $jobToApply->id,
                'status' => $pData['status'],
                'score' => $eval['score'],
                'kbs_status' => $eval['status'],
                'kbs_evaluation' => $eval,
                'applied_at' => now()->subDays(rand(1, 10)),
            ]);

            // Notify user of job match
            $u->notify(new NewJobPostingNotification($jobToApply));
        }

        // Seed initial audit log entries
        AuditLog::create([
            'user_id' => $agencyAdmin->id,
            'actor_name' => $agencyAdmin->name,
            'actor_role' => $agencyAdmin->role,
            'action' => 'SYSTEM_INIT',
            'description' => 'JobSync Platform initialized with 3-Actor RBAC, dynamic match evaluations, and audit logging.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
