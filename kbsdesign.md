# Knowledge Base & Matching Engine Architecture — 3-Actor KBS Design

This document details the architecture and formal design of the **Knowledge-Based Talent Matching System (KBS)**. The platform operates strictly on deterministic rule-based evaluation and multi-criteria decision models (AHP-style scoring), avoiding unexplainable machine learning models.

---

## 1. Core KBS Pillars

1. **Structured Fact Base (`candidate_profiles`, `job_postings`)**
   - Clean, normalized candidate attributes (Education Level, Field Experience Months, Skill arrays, Work History cards) inputted directly via the **Structured Digital CV Builder** or deduced from uploaded documents.
2. **Dynamic Knowledge Base (`matching_criteria`, `matching_rules`, `custom_rules`)**
   - Weighted scoring parameters and IF-THEN logic rules stored as database state, dynamically editable by Agency Staff without modifying application code.
3. **Inference Engine (`App\Services\MatchingEngine`)**
   - A deterministic engine that applies the active Knowledge Base criteria and rules to evaluate any candidate-job pair.
4. **Explanation Facility (`matches.breakdown`, `explanations`)**
   - Computes transparent, human-readable explanations detailing *why* a candidate received a specific score and recommendation status (`recommended`, `flagged`, `excluded`).
5. **Human-in-the-Loop Audit & Verification Subsystem (`agency_admin`)**
   - Enables Agency Staff to audit algorithmic match scores, verify employer legitimacy, validate candidate credential facts, and assist digitally excluded jobseekers.

---

## 2. The 3-Actor RBAC Architecture Model

```
+-----------------------------------------------------------------------------------+
|                              AGENCY SUPER ADMIN                                  |
|                             (role: agency_admin)                                  |
|   - Verifies Employer Legitimacy & Issues Badges                                  |
|   - Validates Candidate Credential Facts                                          |
|   - Audits Algorithm Scores & Human-in-the-Loop Overrides                          |
|   - Performs Assisted Digital Profile Creation (Proxy Entry)                     |
|   - Calibrates KBS Criteria Weights & Global IF-THEN Rules                       |
+----------------------------------------+------------------------------------------+
                                         |
               +-------------------------+-------------------------+
               |                                                   |
+--------------v--------------------+             +----------------v------------------+
|       HIRING EMPLOYER             |             |       JOB SEEKER CANDIDATE        |
|       (role: employer)            |             |        (role: candidate)          |
|  - Manages Org Profile            |             |  - Fills Digital CV Builder Form  |
|  - Posts Job Vacancies            |             |  - Attaches Optional Resume PDF   |
|  - Configures Vacancy KBS Rules   |             |  - Views Match Explanations       |
|  - Reviews Applicant Pool         |             |  - Submits Applications           |
+-----------------------------------+             +-----------------------------------+
```

---

## 3. Database Schema

### `organizations` — Employer Entities & Verification
```php
Schema::create('organizations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
    $table->string('name');
    $table->string('code')->unique();
    $table->string('org_type')->default('PRIVATE_COMPANY'); // PRIVATE_COMPANY, NGO, PARASTATAL, GOV_BODY, UN_AGENCY, INTERNATIONAL_ORG
    $table->string('logo_path')->nullable();
    $table->boolean('is_verified')->default(false);
    $table->timestamp('verified_at')->nullable();
    $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

### `candidate_profiles` — Fact Base & Credentials Verification
```php
Schema::create('candidate_profiles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('education_level')->nullable();
    $table->text('summary')->nullable();
    $table->json('skills')->nullable();
    $table->json('languages')->nullable();
    $table->integer('years_experience')->default(0);
    $table->integer('field_experience_months')->default(0);
    $table->decimal('reliability_score', 4, 2)->nullable();
    $table->json('work_history')->nullable();
    $table->json('education_history')->nullable();
    $table->json('references_list')->nullable();
    $table->string('resume_path')->nullable();
    $table->string('resume_filename')->nullable();
    $table->boolean('is_verified')->default(false);
    $table->timestamp('verified_at')->nullable();
    $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

### `matching_criteria` — AHP Weighted Parameters (Knowledge Base)
```php
Schema::create('matching_criteria', function (Blueprint $table) {
    $table->id();
    $table->string('name');            // e.g. "Skill Match", "Experience", "Reliability"
    $table->string('key')->unique();   // e.g. "skill_match", "experience", "reliability"
    $table->decimal('weight', 4, 2);   // AHP weight (e.g. 0.50, 0.30, 0.20)
    $table->text('description')->nullable();
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

### `matching_rules` — Global IF-THEN Rules Engine
```php
Schema::create('matching_rules', function (Blueprint $table) {
    $table->id();
    $table->string('name');                // e.g. "Minimum Experience Threshold"
    $table->string('field');               // e.g. "years_experience"
    $table->string('operator');            // '>=', '<=', '==', 'contains'
    $table->string('value');               // threshold
    $table->string('action');              // 'exclude', 'flag', 'bonus'
    $table->text('explanation_template');  // e.g. "Meets minimum experience threshold of :value years"
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

### `job_applications` — Submissions & Snapshot Audit Trail
```php
Schema::create('job_applications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_posting_id')->nullable()->constrained('job_postings')->nullOnDelete();
    $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
    $table->string('job_title_snapshot')->nullable();
    $table->string('organization_name_snapshot')->nullable();
    $table->string('status')->default('submitted'); // submitted, shortlisted, interview, hired, rejected
    $table->json('screening_answers')->nullable();
    $table->json('education_data')->nullable();
    $table->json('work_history_data')->nullable();
    $table->json('references_data')->nullable();
    $table->text('motivational_statement')->nullable();
    $table->boolean('integrity_accepted')->default(false);
    $table->boolean('ai_declaration_accepted')->default(false);
    $table->timestamp('applied_at')->useCurrent();
    $table->timestamps();
});
```

---

## 4. Inference Engine Evaluation Workflow

`app/Services/MatchingEngine.php` applies a two-tier evaluation strategy:

1. **Multi-Criteria Scoring (AHP Weighted Criteria):**
   $$\text{Score} = \sum_{c \in \text{Criteria}} \left( \text{Score}_c \times \text{Weight}_c \right)$$
2. **Rule Constraint Checks (IF-THEN Logic & Custom Knockouts):**
   * **Mandatory Knockouts:** Instantly marks candidate status as `excluded`.
   * **Bonus Rules:** Adds score adjustments for matching optional rules.
   * **Flag Rules:** Highlights special candidate facts (*e.g. Field Experience Threshold*).

---

## 5. Human-in-the-Loop & Agency Verification Subsystem

To satisfy enterprise compliance and digital accessibility standards:
- **Agency Super Admin Portal (`/admin/dashboard`):** Allows agency verification officers to inspect organization credentials, audit candidate facts, adjust algorithm criteria weights, and manually register digitally excluded jobseekers via proxy profile entry.