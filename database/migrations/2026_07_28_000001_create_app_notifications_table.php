<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table is named "app_notifications" (not "notifications") because User already
     * uses Laravel's built-in Notifiable trait, which expects the standard Laravel
     * notifications schema — reusing that table name with our own simpler schema
     * would collide with it.
     */
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // recipient
            $table->string('type'); // appointment_approved, appointment_rejected, appointment_completed, new_request, almost_done
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
