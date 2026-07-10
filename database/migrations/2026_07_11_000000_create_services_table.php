<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // Grooming Packages | Grooming Services | Other Services
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            // Grooming Packages
            ['BP Signature', 'Grooming Packages'],
            ['Deluxe',       'Grooming Packages'],
            ['Grande',       'Grooming Packages'],
            ['Premium',      'Grooming Packages'],
            ['Standard',     'Grooming Packages'],
            // Grooming Services
            ['Bath & Dry',            'Grooming Services'],
            ['De-shedding Treatment', 'Grooming Services'],
            ['Ear Cleaning',          'Grooming Services'],
            ['Flea & Tick Treatment','Grooming Services'],
            ['Full Grooming',         'Grooming Services'],
            ['Haircut & Styling',     'Grooming Services'],
            ['Nail Trimming',         'Grooming Services'],
            ['Paw Treatment',         'Grooming Services'],
            ['Teeth Brushing',        'Grooming Services'],
            // Other Services
            ['Boarding Checkup', 'Other Services'],
            ['Follow-up',        'Other Services'],
            ['Other',            'Other Services'],
        ];

        foreach ($rows as [$name, $category]) {
            DB::table('services')->insert([
                'name' => $name, 'category' => $category, 'is_active' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};