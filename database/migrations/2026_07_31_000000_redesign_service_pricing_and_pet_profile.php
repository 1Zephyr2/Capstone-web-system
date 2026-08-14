<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Replace the flat price column with per-size JSON pricing — real prices
        // vary by pet size (XS/S/M/L/XL/G), not a single flat number per service.
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('price');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->json('prices')->nullable()->after('category');
        });

        // Wipe the placeholder catalog and reseed with the real Bark Pack price list
        DB::table('services')->truncate();

        $now = now();
        $rows = [
            // Grooming Packages — price by size: XS, S, M, L, XL, G
            ['Premium Package',  'Grooming Packages', [400, 500, 600, 1100, 1300, 1500]],
            ['Deluxe Package',   'Grooming Packages', [550, 650, 800, 1250, 1500, 2000]],
            ['Grande Package',   'Grooming Packages', [700, 800, 900, 1500, 2000, 2500]],
            ['Signature Package','Grooming Packages', [800, 900, 1000, 1600, 2200, 3000]],

            // Add-ons / Treatments
            ['BP - Odor',              'Add-ons', [100, 150, 200, 300, 500, 750]],
            ['BP - Extra White',       'Add-ons', [50, 100, 150, 250, 350, 500]],
            ['BP - ATF (Tick & Flea)', 'Add-ons', [50, 100, 150, 200, 300, 500]],
            ['BP - AM (Medicated)',    'Add-ons', [70, 150, 200, 300, 450, 700]],
            ['BP - AHS (Herb Spa)',    'Add-ons', null], // flat 500/pack, handled below

            // Ala Carte
            ['Ear Wax Solution',        'Ala Carte', [30, 50, 50, 70, 70, 100]],
            ['Ear Plucking & Cleaning', 'Ala Carte', [120, 150, 150, 170, 170, 200]],
            ['Toothbrushing or Gel',    'Ala Carte', [120, 150, 150, 170, 170, 200]],
            ['Face Trim',               'Ala Carte', [200, 350, 400, 650, 750, 800]],
            ['Nail Cut & File',         'Ala Carte', [120, 150, 150, 180, 180, 250]],
            ['Paw Pad Trim',            'Ala Carte', [100, 120, 120, 170, 170, 220]],
            ['Poodle Feet',             'Ala Carte', [200, 300, 400, 750, 800, 1000]],
            ['Anal Sac Draining',       'Ala Carte', [250, 300, 400, 750, 800, 900]],
            ['Extra Blower',            'Ala Carte', [250, 300, 400, 750, 900, 1300]],
            ['De-matting',              'Ala Carte', [300, 350, 400, 500, 600, 850]],
            ['De-shedding',             'Ala Carte', [300, 350, 400, 500, 600, 850]],
        ];

        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'G'];

        foreach ($rows as [$name, $category, $priceList]) {
            $prices = $priceList === null
                ? ['flat' => 500] // BP - AHS (Herb Spa) is a flat 500/pack rate
                : array_combine($sizes, $priceList);

            DB::table('services')->insert([
                'name'        => $name,
                'category'    => $category,
                'prices'      => json_encode($prices),
                'is_active'   => true,
                'is_archived' => false,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // Pet sizes now match the real pricing tiers (XS/S/M/L/XL/G) instead of the
        // old generic Small/Medium/Large/Extra Large marketing categories.
        Schema::table('pets', function (Blueprint $table) {
            $table->text('vaccination_record')->nullable()->after('size');
            $table->text('medical_conditions')->nullable()->after('vaccination_record');
            $table->text('grooming_triggers')->nullable()->after('medical_conditions');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('prices');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('category');
        });
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['vaccination_record', 'medical_conditions', 'grooming_triggers']);
        });
    }
};
