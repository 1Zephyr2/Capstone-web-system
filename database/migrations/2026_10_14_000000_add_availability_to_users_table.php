<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('availability')->default('available')->after('role');
            $table->string('availability_note')->nullable()->after('availability');
            $table->timestamp('availability_updated_at')->nullable()->after('availability_note');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['availability', 'availability_note', 'availability_updated_at']);
        });
    }
};
