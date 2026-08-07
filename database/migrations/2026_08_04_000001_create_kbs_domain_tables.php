<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('org_type')->default('UN_AGENCY'); // UN_AGENCY, GOV_BODY, INGO, MULTILATERAL, CORPORATE
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('title');
            $table->string('grade')->default('P-3'); // P-1 to P-5, D-1/D-2, GS-7, GS-9 to GS-14
            $table->string('location');
            $table->boolean('is_remote')->default(false);
            $table->text('description');
            $table->integer('min_experience')->default(0);
            $table->json('required_skills')->nullable();
            $table->json('required_languages')->nullable();
            $table->timestamps();
        });

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
            $table->timestamps();
        });

        Schema::create('matching_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // e.g. "Skill Match", "Experience", "Reliability"
            $table->string('key')->unique();   // e.g. "skill_match", "experience", "reliability"
            $table->decimal('weight', 4, 2);   // AHP-derived weight, sums to 1.00
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('matching_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');                // e.g. "Minimum Experience Threshold"
            $table->string('field');               // e.g. "years_experience"
            $table->string('operator');            // '>=', '<=', '==', 'contains'
            $table->string('value');               // threshold
            $table->string('action');              // 'exclude', 'flag', 'bonus'
            $table->text('explanation_template');  // e.g. "Meets minimum experience requirement of :value years"
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->decimal('score', 5, 2);        // 0.00 to 100.00
            $table->json('breakdown');             // Per-criterion raw score + weight + contribution
            $table->json('explanations')->nullable(); // Human-readable explanations
            $table->string('status');              // 'recommended', 'flagged', 'excluded'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
        Schema::dropIfExists('matching_rules');
        Schema::dropIfExists('matching_criteria');
        Schema::dropIfExists('candidate_profiles');
        Schema::dropIfExists('job_postings');
        Schema::dropIfExists('organizations');
    }
};
