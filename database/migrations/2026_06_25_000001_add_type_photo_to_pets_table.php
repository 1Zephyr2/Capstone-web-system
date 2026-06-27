<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            if (!Schema::hasColumn('pets', 'type')) {
                $table->string('type')->default('dog')->after('name');
            }
            if (!Schema::hasColumn('pets', 'photo')) {
                $table->string('photo')->nullable()->after('special_notes');
            }
        });

        // Also add notes/rejection_reason to appointments if missing
        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                if (!Schema::hasColumn('appointments', 'notes')) {
                    $table->text('notes')->nullable()->after('status');
                }
                if (!Schema::hasColumn('appointments', 'rejection_reason')) {
                    $table->string('rejection_reason')->nullable()->after('notes');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['type', 'photo']);
        });

        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn(['notes', 'rejection_reason']);
            });
        }
    }
};