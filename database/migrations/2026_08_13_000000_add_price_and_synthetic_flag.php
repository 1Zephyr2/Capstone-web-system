<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Price captured at booking time (from the service's price for that
            // pet's size). Kept on the appointment itself rather than recomputed
            // later, so historical revenue stays accurate even if a service's
            // prices change afterward.
            $table->decimal('price', 10, 2)->nullable()->after('service_id');

            // Flags rows created by the demo/dummy data generator so they can be
            // told apart from real bookings and safely removed later.
            $table->boolean('is_synthetic')->default(false)->after('price');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_synthetic')->default(false)->after('role');
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->boolean('is_synthetic')->default(false)->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['price', 'is_synthetic']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_synthetic');
        });
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('is_synthetic');
        });
    }
};
