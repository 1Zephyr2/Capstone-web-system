<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', fn (Blueprint $table) => $table->date('birthday')->nullable());
        Schema::table('appointments', function (Blueprint $table) {
            $table->json('service_extras')->nullable();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_staff_id');
            $table->dropColumn('service_extras');
        });
        Schema::table('pets', fn (Blueprint $table) => $table->dropColumn('birthday'));
    }
};
