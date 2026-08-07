<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['job_posting_id']);
            $table->foreignId('job_posting_id')->nullable()->change()->constrained('job_postings')->nullOnDelete();
            $table->string('job_title_snapshot')->nullable()->after('candidate_id');
            $table->string('organization_name_snapshot')->nullable()->after('job_title_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['job_title_snapshot', 'organization_name_snapshot']);
        });
    }
};
