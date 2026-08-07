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
        if (Schema::hasTable('job_postings')) {
            Schema::table('job_postings', function (Blueprint $table) {
                if (! Schema::hasColumn('job_postings', 'custom_rules')) {
                    $table->json('custom_rules')->nullable()->after('required_languages');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_postings')) {
            Schema::table('job_postings', function (Blueprint $table) {
                if (Schema::hasColumn('job_postings', 'custom_rules')) {
                    $table->dropColumn('custom_rules');
                }
            });
        }
    }
};
