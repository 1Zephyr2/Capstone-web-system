<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AppointmentSeeder extends Seeder
{
    /**
     * All sample data uses the @sample.test email domain so it's easy to find
     * and delete later — e.g. User::where('email', 'like', '%@sample.test')->delete()
     * (pets and appointments cascade-delete with their owner).
     */
    const SAMPLE_DOMAIN = 'sample.test';

    public function run(): void
    {
        // Wipe only previously-seeded sample data (not real customer data)
        $oldSampleUserIds = User::where('email', 'like', '%@' . self::SAMPLE_DOMAIN)->pluck('id');
        Schema::disableForeignKeyConstraints();
        Appointment::whereIn('user_id', $oldSampleUserIds)->delete();
        Pet::whereIn('user_id', $oldSampleUserIds)->delete();
        User::whereIn('id', $oldSampleUserIds)->delete();
        Schema::enableForeignKeyConstraints();

        $owners = [
            ['name' => 'Shamaimah Reyes',   'phone' => '0917 111 2222', 'pets' => [['Max', 'dog', 'Golden Retriever', 3], ['Coco', 'cat', 'Siamese', 2]]],
            ['name' => 'Medge Villanueva',  'phone' => '0917 222 3333', 'pets' => [['Luna', 'cat', 'Persian Cat', 4]]],
            ['name' => 'Carlos Dizon',      'phone' => '0917 333 4444', 'pets' => [['Bruno', 'dog', 'Shih Tzu', 5], ['Milo', 'dog', 'Chihuahua', 1]]],
            ['name' => 'Angela Cruz',       'phone' => '0917 444 5555', 'pets' => [['Bella', 'dog', 'Beagle', 2]]],
            ['name' => 'Ramon Santiago',    'phone' => '0917 555 6666', 'pets' => [['Ziggy', 'dog', 'Pomeranian', 3], ['Whiskers', 'cat', 'Maine Coon', 6]]],
            ['name' => 'Kendra Magno',      'phone' => '0917 666 7777', 'pets' => [['Ara', 'cat', 'Persian', 1]]],
        ];

        $pets = collect();

        foreach ($owners as $i => $data) {
            $email = 'owner' . ($i + 1) . '@' . self::SAMPLE_DOMAIN;
            $owner = User::create([
                'name'     => $data['name'],
                'email'    => $email,
                'phone'    => $data['phone'],
                'password' => Hash::make('password'),
                'role'     => 'owner',
            ]);

            foreach ($data['pets'] as [$name, $type, $breed, $age]) {
                $pets->push(Pet::create([
                    'user_id' => $owner->id,
                    'name'    => $name,
                    'type'    => $type,
                    'breed'   => $breed,
                    'age'     => $age,
                    // Without a size, priceForSize() has nothing to match and every
                    // seeded appointment's price silently ends up null — pick one
                    // roughly matching breed so revenue numbers look plausible too.
                    'size'    => self::sizeForBreed($breed),
                ]));
            }
        }

        $services = Service::active()->get();

        if ($services->isEmpty()) {
            $this->command->warn('No active services found — skipping appointment seeding. Add services first.');
            return;
        }

        $hours = array_keys(Appointment::CLINIC_HOURS);
        $statusPool = [
            Appointment::STATUS_PENDING,
            Appointment::STATUS_APPROVED,
            Appointment::STATUS_COMPLETED,
            Appointment::STATUS_COMPLETED,
            Appointment::STATUS_COMPLETED, // weighted heavier so most of the past reads as resolved history
            Appointment::STATUS_REJECTED,
            Appointment::STATUS_CANCELLED,
        ];

        $notesPool = [
            'Nervous around strangers, please be gentle.',
            'Check ears for infection.',
            'Prefers a quiet room.',
            'Allergic to certain shampoos — use hypoallergenic.',
            'First time grooming, may be anxious.',
            null, null, null,
        ];

        // Mild, believable seasonality (not a sine wave, just plausible highs/
        // lows a real grooming shop might see) so the forecasting engine has
        // an actual seasonal pattern to detect instead of flat noise:
        // slower right after New Year, busier around summer and December.
        $monthWeight = [
            1 => 0.7, 2 => 0.8, 3 => 0.9, 4 => 1.0, 5 => 1.1, 6 => 1.2,
            7 => 1.15, 8 => 1.0, 9 => 0.9, 10 => 1.0, 11 => 1.1, 12 => 1.35,
        ];

        $created = 0;
        $slotUsage = []; // track "date|time" => count, so we respect MAX_PER_SLOT within this seed run

        // Span roughly one full year back (so the ensemble has a whole
        // seasonal cycle to learn from) through 3 weeks into the future
        // (so there's still a pending/upcoming queue to work with).
        $startDate = now()->copy()->subYear()->startOfMonth();
        $endDate   = now()->copy()->addWeeks(3);
        $totalDays = $startDate->diffInDays($endDate);

        foreach ($services as $service) {
            // More bookings per service since this now spans a year instead
            // of ~80 days — keeps the same "appointments per week" density.
            $bookingsForThisService = rand(45, 90);

            for ($b = 0; $b < $bookingsForThisService; $b++) {
                $date = $startDate->copy()->addDays(rand(0, $totalDays));

                // Clinic is closed weekends (matches the real booking rules) —
                // nudge onto the nearest weekday instead of just skipping,
                // so the requested density still roughly holds.
                if ($date->isWeekend()) {
                    $date = $date->isSaturday() ? $date->addDays(2) : $date->addDay();
                }

                // Seasonal thinning: roll again against that month's weight,
                // more likely to be discarded in a historically "slow" month.
                if (mt_rand() / mt_getrandmax() > ($monthWeight[$date->month] ?? 1.0)) {
                    continue;
                }

                $dayOffset = (int) now()->startOfDay()->diffInDays($date, false);
                $hour = $hours[array_rand($hours)];
                $datetimeKey = $date->format('Y-m-d') . '|' . $hour;

                $slotUsage[$datetimeKey] = $slotUsage[$datetimeKey] ?? 0;
                if ($slotUsage[$datetimeKey] >= Appointment::MAX_PER_SLOT) {
                    continue; // slot full for this seed run, skip rather than overbook
                }

                $pet = $pets->random();
                $status = $dayOffset < 0
                    ? collect($statusPool)->random() // past dates: any resolved status
                    : ($dayOffset === 0
                        ? collect([Appointment::STATUS_APPROVED, Appointment::STATUS_PENDING])->random()
                        : Appointment::STATUS_PENDING); // future: still pending review

                $datetime = $date->copy()->setTimeFromTimeString($hour . ':00');

                // Avoid exact duplicate pet+slot from an earlier iteration
                $exists = Appointment::where('pet_id', $pet->id)->where('appointment_date', $datetime)->exists();
                if ($exists) {
                    continue;
                }

                Appointment::create([
                    'user_id'          => $pet->user_id,
                    'pet_id'           => $pet->id,
                    'service_id'       => $service->id,
                    'appointment_date' => $datetime,
                    'status'           => $status,
                    'price'            => $service->priceForSize($pet->size),
                    'notes'            => $notesPool[array_rand($notesPool)],
                    'rejection_reason' => $status === Appointment::STATUS_REJECTED ? 'Slot unavailable, please reschedule.' : null,
                    'booking_group_id' => (string) Str::uuid(),
                ]);

                $slotUsage[$datetimeKey]++;
                $created++;
            }
        }

        $this->command->info('Seeded ' . count($owners) . " sample owners, {$pets->count()} pets, and {$created} appointments spanning " . $startDate->format('M Y') . ' – ' . $endDate->format('M Y') . ' across ' . $services->count() . ' active services.');
    }

    /**
     * Rough, plausible pet size for a given breed, matching Pet::SIZES —
     * only used so seeded appointments have a real price instead of null.
     */
    private static function sizeForBreed(string $breed): string
    {
        return match (true) {
            str_contains($breed, 'Chihuahua') => 'XS',
            str_contains($breed, 'Shih Tzu'), str_contains($breed, 'Pomeranian') => 'S',
            str_contains($breed, 'Beagle'), str_contains($breed, 'Siamese'), str_contains($breed, 'Persian') => 'S',
            str_contains($breed, 'Maine Coon') => 'M',
            str_contains($breed, 'Golden Retriever') => 'L',
            default => ['XS', 'S', 'M', 'L'][array_rand(['XS', 'S', 'M', 'L'])],
        };
    }
}
