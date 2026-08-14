<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->string('size')->nullable()->after('breed'); // Small / Medium / Large / Extra Large
        });

        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('category');
        });

        Schema::table('pet_records', function (Blueprint $table) {
            $table->string('record_type')->default('checkup')->after('pet_id'); // checkup | vaccination | note
            $table->string('vaccine_name')->nullable()->after('diagnosis');
            $table->date('next_due_date')->nullable()->after('vaccine_name'); // next vaccination/checkup due
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('size');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('price');
        });
        Schema::table('pet_records', function (Blueprint $table) {
            $table->dropColumn(['record_type', 'vaccine_name', 'next_due_date']);
        });
    }
};
