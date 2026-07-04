<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grooming_options', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'style' or 'addon'
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default styles
        DB::table('grooming_options')->insert([
            ['type' => 'style', 'name' => 'Puppy Cut',    'description' => 'Short, uniform length all over',      'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'style', 'name' => 'Lion Cut',     'description' => 'Shaved body with full mane and tail', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'style', 'name' => 'Teddy Bear',   'description' => 'Rounded, fluffy look',                'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'style', 'name' => 'Summer Cut',   'description' => 'Very short for hot weather',          'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'addon', 'name' => 'Teeth Cleaning','description' => 'Brush and freshen breath',           'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'addon', 'name' => 'Flea Treatment','description' => 'Anti-flea shampoo and treatment',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'addon', 'name' => 'Ear Cleaning', 'description' => 'Deep clean ear canal',               'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['type' => 'addon', 'name' => 'Nail Trim',    'description' => 'Clip and file nails',                'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('grooming_options');
    }
};