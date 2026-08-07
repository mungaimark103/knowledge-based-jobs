<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('submitted'); // draft, submitted, shortlisted, interview, hired, rejected
            $table->json('screening_answers')->nullable();
            $table->json('education_data')->nullable();
            $table->json('work_history_data')->nullable();
            $table->json('references_data')->nullable();
            $table->text('motivational_statement')->nullable();
            $table->boolean('integrity_accepted')->default(false);
            $table->boolean('ai_declaration_accepted')->default(false);
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();

            $table->unique(['job_posting_id', 'candidate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
