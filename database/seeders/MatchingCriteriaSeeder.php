<?php

namespace Database\Seeders;

use App\Models\CandidateProfile;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\MatchingCriterion;
use App\Models\MatchingRule;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        // 1. Seed 10 Organizations across diverse sectors
        $orgTemplates = [
            [
                'name' => 'Safaricom PLC',
                'code' => 'SAFARICOM',
                'org_type' => 'PRIVATE_COMPANY',
                'vision' => 'Transforming lives through technology and digital financial solutions.',
                'about_us' => 'Safaricom is Kenya\'s leading telecommunications provider and creator of M-PESA.',
                'logo_path' => 'https://upload.wikimedia.org/wikipedia/commons/e/ed/UNICEF_Logo.svg',
            ],
            [
                'name' => 'Google Technology Global',
                'code' => 'GOOGLE',
                'org_type' => 'PRIVATE_COMPANY',
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
                'name' => 'Doctors Without Borders (MSF)',
                'code' => 'MSF',
                'org_type' => 'NGO',
                'vision' => 'Providing medical assistance to people affected by conflict, epidemics, or disasters.',
                'about_us' => 'International independent medical humanitarian organization operating globally.',
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
                'name' => 'Energy & Petroleum Regulatory Authority',
                'code' => 'EPRA',
                'org_type' => 'PARASTATAL',
                'vision' => 'Regulating energy sectors for sustainable economic growth and public safety.',
                'about_us' => 'Independent regulatory body for electrical, oil, gas, and renewable energy sectors.',
                'logo_path' => null,
            ],
            [
                'name' => 'Ministry of Foreign Affairs',
                'code' => 'MFA',
                'org_type' => 'GOV_BODY',
                'vision' => 'Advancing diplomatic relations and international trade partnerships.',
                'about_us' => 'Government ministry executing national foreign policy and diplomatic engagements.',
                'logo_path' => null,
            ],
            [
                'name' => 'UNICEF',
                'code' => 'UNICEF',
                'org_type' => 'UN_AGENCY',
                'vision' => 'Advocating for child rights, survival, and protection worldwide.',
                'about_us' => 'UNICEF works in over 190 countries to reach the most disadvantaged children.',
                'logo_path' => 'https://upload.wikimedia.org/wikipedia/commons/e/ed/UNICEF_Logo.svg',
            ],
            [
                'name' => 'United Nations Development Programme',
                'code' => 'UNDP',
                'org_type' => 'UN_AGENCY',
                'vision' => 'Eradicating poverty and reducing inequalities through sustainable development.',
                'about_us' => 'UNDP is the United Nations\' global development network operating in 170+ countries.',
                'logo_path' => null,
            ],
            [
                'name' => 'World Health Organization (WHO)',
                'code' => 'WHO',
                'org_type' => 'INTERNATIONAL_ORG',
                'vision' => 'Promoting global health, keeping the world safe, and serving the vulnerable.',
                'about_us' => 'Specialized agency responsible for international public health response and standards.',
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
            $organizations[] = Organization::create($ot);
        }

        // 2. Base Matching Criteria & Standard KBS Rules
        MatchingCriterion::updateOrCreate(['key' => 'skill_match'], ['name' => 'Skill Match', 'weight' => 0.50, 'description' => 'Percentage of required job skills matched in candidate profile', 'active' => true]);
        MatchingCriterion::updateOrCreate(['key' => 'experience'], ['name' => 'Experience', 'weight' => 0.30, 'description' => 'Candidate experience years relative to job minimum', 'active' => true]);
        MatchingCriterion::updateOrCreate(['key' => 'reliability'], ['name' => 'Reliability', 'weight' => 0.20, 'description' => 'Historical candidate reliability and reference score', 'active' => true]);

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
            ['name' => 'High Background Reliability Bonus'],
            [
                'field' => 'reliability_score',
                'operator' => '>=',
                'value' => '85',
                'action' => 'bonus',
                'explanation_template' => 'Verified high background reliability score (>= :value%)',
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
                'explanation_template' => 'Official KBS Verified Agency Badge awarded',
                'active' => true,
            ]
        );

        // Universal Job Grades: Entry Level, Junior, Mid Level / Associate, Professional, Senior Professional, Executive / Lead
        $jobTitles = [
            ['title' => 'Software Engineer & API Lead', 'grade' => 'Mid Level / Associate', 'skills' => ['PHP', 'Laravel', 'Vue.js', 'SQL', 'Git']],
            ['title' => 'Senior Cloud Architect', 'grade' => 'Executive / Lead', 'skills' => ['AWS', 'Docker', 'Kubernetes', 'Python', 'DevOps']],
            ['title' => 'Junior Data Analyst', 'grade' => 'Junior', 'skills' => ['Data Analysis', 'SQL', 'Python']],
            ['title' => 'Enterprise System Administrator', 'grade' => 'Professional', 'skills' => ['System Architecture', 'AWS', 'Docker']],
            ['title' => 'Product Design & UI/UX Specialist', 'grade' => 'Mid Level / Associate', 'skills' => ['Figma', 'UI/UX Design', 'Typography', 'Adobe Creative Cloud']],
            ['title' => 'Financial Audit Associate', 'grade' => 'Entry Level', 'skills' => ['Financial Accounting', 'Auditing', 'Bookkeeping']],
            ['title' => 'Senior Tax Compliance Manager', 'grade' => 'Senior Professional', 'skills' => ['Tax Compliance', 'IFRS', 'Auditing', 'QuickBooks']],
            ['title' => 'Public Health Operations Lead', 'grade' => 'Senior Professional', 'skills' => ['Public Health', 'Clinical Protocols', 'Patient Care', 'Emergency Care']],
            ['title' => 'Clinical Research Specialist', 'grade' => 'Professional', 'skills' => ['Medical Research', 'Clinical Diagnostics', 'Public Health']],
            ['title' => 'Senior Monitoring & Evaluation Specialist', 'grade' => 'Senior Professional', 'skills' => ['Monitoring & Evaluation', 'Data Analysis', 'Project Management']],
            ['title' => 'Global Climate Resilience Director', 'grade' => 'Executive / Lead', 'skills' => ['Climate Policy', 'Public Policy', 'Grant Writing', 'Stakeholder Engagement']],
            ['title' => 'Legal Counsel & Regulatory Compliance Officer', 'grade' => 'Professional', 'skills' => ['Contract Law', 'Regulatory Compliance', 'Due Diligence', 'Litigation']],
            ['title' => 'International Trade & Diplomacy Delegate', 'grade' => 'Senior Professional', 'skills' => ['Public Policy', 'Arbitration', 'Contract Law', 'Stakeholder Engagement']],
            ['title' => 'Humanitarian Relief Coordinator', 'grade' => 'Mid Level / Associate', 'skills' => ['Humanitarian Action', 'Project Management', 'Risk Assessment']],
            ['title' => 'Energy Sector Grid Inspector', 'grade' => 'Junior', 'skills' => ['Risk Assessment', 'Regulatory Compliance', 'Data Analysis']],
        ];

        $locations = ['Nairobi, Kenya', 'Geneva, Switzerland', 'New York, USA', 'Tokyo, Japan', 'London, UK', 'Berlin, Germany', 'Remote / Global'];

        // 3. Seed 30 Job Postings (3 jobs per company)
        $createdJobs = [];
        $jobIndex = 0;
        foreach ($organizations as $org) {
            for ($i = 0; $i < 3; $i++) {
                $template = $jobTitles[$jobIndex % count($jobTitles)];
                $jobIndex++;

                $minExp = match ($template['grade']) {
                    'Entry Level' => 1,
                    'Junior' => 2,
                    'Mid Level / Associate' => 3,
                    'Professional' => 5,
                    'Senior Professional' => 8,
                    'Executive / Lead' => 10,
                    default => 4,
                };

                $createdJobs[] = JobPosting::create([
                    'organization_id' => $org->id,
                    'title' => $template['title'] . " ({$org->code})",
                    'grade' => $template['grade'],
                    'location' => $locations[rand(0, count($locations) - 1)],
                    'is_remote' => rand(0, 1) === 1,
                    'description' => "Drive operational excellence and strategic impact for {$org->name}. Seeking qualified talent for key deliverables.",
                    'min_experience' => $minExp,
                    'required_skills' => $template['skills'],
                    'required_languages' => ['English'],
                ]);
            }
        }

        // 4. Seed 50 Candidates & Candidate Profiles
        $firstNames = ['James', 'Mary', 'John', 'Patricia', 'Robert', 'Jennifer', 'Michael', 'Linda', 'William', 'Elizabeth', 'David', 'Barbara', 'Richard', 'Susan', 'Joseph', 'Jessica', 'Thomas', 'Sarah', 'Charles', 'Karen', 'Christopher', 'Nancy', 'Daniel', 'Lisa', 'Matthew', 'Betty', 'Anthony', 'Margaret', 'Donald', 'Sandra'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson'];

        $educationOptions = [
            'Bachelor\'s Degree',
            'Master\'s Degree',
            'Doctorate (Ph.D. / M.D.)',
            'Diploma / Associate',
            'High School / Secondary',
        ];

        $skillsPool = [
            'PHP', 'Laravel', 'Vue.js', 'React', 'Python', 'AWS', 'Docker', 'Kubernetes', 'SQL', 'DevOps',
            'Data Analysis', 'Monitoring & Evaluation', 'Project Management', 'Public Policy', 'Climate Policy',
            'Grant Writing', 'Risk Assessment', 'Tax Compliance', 'Financial Accounting', 'Auditing', 'IFRS',
            'Public Health', 'Clinical Protocols', 'Patient Care', 'Contract Law', 'Regulatory Compliance',
        ];

        $candidates = [];
        for ($c = 1; $c <= 50; $c++) {
            $fname = $firstNames[($c - 1) % count($firstNames)];
            $lname = $lastNames[($c * 3) % count($lastNames)];
            $name = "{$fname} {$lname}";
            $email = "candidate{$c}@example.com";

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'candidate',
            ]);

            // Randomize candidate qualifications
            $yearsExp = rand(1, 14);
            $edu = $educationOptions[rand(0, count($educationOptions) - 1)];

            // Pick 3-6 random skills from pool
            shuffle($skillsPool);
            $candSkills = array_slice($skillsPool, 0, rand(3, 6));

            $reliability = round(65.0 + (rand(0, 300) / 10), 1);

            $profile = CandidateProfile::create([
                'user_id' => $user->id,
                'education_level' => $edu,
                'skills' => $candSkills,
                'years_experience' => $yearsExp,
                'field_experience_months' => $yearsExp * 12,
                'reliability_score' => $reliability,
                'languages' => ['English', 'French', 'Swahili'],
                'summary' => "Experienced {$edu} candidate with {$yearsExp} years background in " . implode(', ', array_slice($candSkills, 0, 3)) . '.',
            ]);

            $candidates[] = $user;
        }

        // 5. Create Random Applications (Each candidate applies for 2 to 5 jobs)
        foreach ($candidates as $cand) {
            // Pick 2-5 distinct random jobs
            $jobKeys = array_rand($createdJobs, rand(2, 5));
            if (! is_array($jobKeys)) {
                $jobKeys = [$jobKeys];
            }

            foreach ($jobKeys as $k) {
                $job = $createdJobs[$k];
                JobApplication::firstOrCreate([
                    'job_posting_id' => $job->id,
                    'candidate_id' => $cand->id,
                ], [
                    'status' => 'submitted',
                    'applied_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
