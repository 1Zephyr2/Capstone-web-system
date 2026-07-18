<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Result photo of the pet after service is finished (item 2)
            $table->string('result_photo')->nullable()->after('notes');

            // Pickup tracking — only filled when a different person picks up (item 3)
            $table->boolean('different_pickup')->default(false)->after('result_photo');
            $table->string('picked_up_by')->nullable()->after('different_pickup');
            $table->text('pickup_note')->nullable()->after('picked_up_by');

            // Groups multiple pets booked together in one submission (item 5)
            $table->string('booking_group_id')->nullable()->after('pickup_note');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['result_photo', 'different_pickup', 'picked_up_by', 'pickup_note', 'booking_group_id']);
        });
    }
};
