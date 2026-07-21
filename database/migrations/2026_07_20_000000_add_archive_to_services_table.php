<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('is_active');
        });

        // Grooming-only shop — archive the old "Other Services" category (Boarding
        // Checkup, Follow-up, Other) instead of deleting, so it can be restored later.
        DB::table('services')
            ->where('category', 'Other Services')
            ->update(['is_archived' => true, 'is_active' => false]);
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });
    }
};
