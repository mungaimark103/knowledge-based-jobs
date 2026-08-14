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
        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->string('recommendation_letter_path')->nullable()->after('resume_filename');
            $table->string('recommendation_letter_filename')->nullable()->after('recommendation_letter_path');

            $table->string('references_doc_path')->nullable()->after('recommendation_letter_filename');
            $table->string('references_doc_filename')->nullable()->after('references_doc_path');

            $table->string('portfolio_doc_path')->nullable()->after('references_doc_filename');
            $table->string('portfolio_doc_filename')->nullable()->after('portfolio_doc_path');

            $table->string('transcripts_doc_path')->nullable()->after('portfolio_doc_filename');
            $table->string('transcripts_doc_filename')->nullable()->after('transcripts_doc_path');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name');
            $table->string('actor_role');
            $table->string('action'); // LOGIN, LOGOUT, PROFILE_UPDATE, DOCUMENT_UPLOAD, APPLICATION_SUBMIT, JOB_CREATE, JOB_DELETE, RULE_UPDATE
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('changes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');

        Schema::table('candidate_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'recommendation_letter_path',
                'recommendation_letter_filename',
                'references_doc_path',
                'references_doc_filename',
                'portfolio_doc_path',
                'portfolio_doc_filename',
                'transcripts_doc_path',
                'transcripts_doc_filename',
            ]);
        });
    }
};
