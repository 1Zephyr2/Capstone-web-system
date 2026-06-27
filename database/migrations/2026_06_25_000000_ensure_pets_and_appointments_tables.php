<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add photo column if pets table already exists
        if (Schema::hasTable('pets') && !Schema::hasColumn('pets', 'photo')) {
            Schema::table('pets', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('special_notes');
            });
        }

        // Create pets table fresh if it doesn't exist yet
        if (!Schema::hasTable('pets')) {
            Schema::create('pets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('type')->default('dog'); // dog, cat, other
                $table->string('breed');
                $table->integer('age');
                $table->text('special_notes')->nullable();
                $table->string('photo')->nullable();
                $table->timestamps();
            });
        }

        // Create appointments table if it doesn't exist
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('pet_id')->constrained()->onDelete('cascade');
                $table->dateTime('appointment_date');
                $table->string('service_type');
                $table->string('status')->default('pending');
                $table->text('notes')->nullable();
                $table->string('rejection_reason')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('pets');
    }
};