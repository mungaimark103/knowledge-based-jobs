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
        if (Schema::hasTable('organizations')) {
            Schema::table('organizations', function (Blueprint $table) {
                if (! Schema::hasColumn('organizations', 'vision')) {
                    $table->text('vision')->nullable()->after('org_type');
                }
                if (! Schema::hasColumn('organizations', 'about_us')) {
                    $table->text('about_us')->nullable()->after('vision');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('organizations')) {
            Schema::table('organizations', function (Blueprint $table) {
                if (Schema::hasColumn('organizations', 'vision')) {
                    $table->dropColumn('vision');
                }
                if (Schema::hasColumn('organizations', 'about_us')) {
                    $table->dropColumn('about_us');
                }
            });
        }
    }
};
