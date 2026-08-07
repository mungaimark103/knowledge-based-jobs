<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add role to users table if it doesn't exist
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('candidate')->after('email'); // candidate, employer, admin
            });
        }

        // 2. Normalized Atomic Skills Table
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category')->default('General');
            $table->timestamps();
        });

        // 3. Normalized Atomic Languages Table
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code', 5)->unique();
            $table->timestamps();
        });

        // 4. Candidate Skills Pivot
        Schema::create('candidate_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_profile_id')->constrained('candidate_profiles')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['candidate_profile_id', 'skill_id']);
        });

        // 5. Job Posting Skills Pivot
        Schema::create('job_posting_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(['job_posting_id', 'skill_id']);
        });

        // 6. Candidate Languages Pivot
        Schema::create('candidate_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_profile_id')->constrained('candidate_profiles')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('proficiency_level')->default('B2'); // A1, A2, B1, B2, C1, C2, Native
            $table->timestamps();

            $table->unique(['candidate_profile_id', 'language_id']);
        });

        // 7. Job Posting Languages Pivot
        Schema::create('job_posting_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('min_level')->default('B2');
            $table->timestamps();

            $table->unique(['job_posting_id', 'language_id']);
        });

        // 8. Organization Reusable KBS Rule Templates
        Schema::create('kbs_rule_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('name');
            $table->json('criteria_weights');
            $table->json('rules_config');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kbs_rule_templates');
        Schema::dropIfExists('job_posting_languages');
        Schema::dropIfExists('candidate_languages');
        Schema::dropIfExists('job_posting_skills');
        Schema::dropIfExists('candidate_skills');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('skills');

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
