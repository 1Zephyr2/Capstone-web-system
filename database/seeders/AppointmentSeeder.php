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
            Appointment::STATUS_COMPLETED, // weighted so "completed" is common, like a real shop history
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

        $created = 0;
        $slotUsage = []; // track "date|time" => count, so we respect MAX_PER_SLOT within this seed run

        // Spread across the last 2 months through the next 3 weeks, so Insights'
        // monthly trend and service popularity both have real variety to show.
        foreach ($services as $service) {
            $bookingsForThisService = rand(3, 6);

            for ($b = 0; $b < $bookingsForThisService; $b++) {
                $dayOffset = rand(-60, 21);
                $date = now()->addDays($dayOffset)->startOfDay();
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
                    'notes'            => $notesPool[array_rand($notesPool)],
                    'rejection_reason' => $status === Appointment::STATUS_REJECTED ? 'Slot unavailable, please reschedule.' : null,
                    'booking_group_id' => (string) Str::uuid(),
                ]);

                $slotUsage[$datetimeKey]++;
                $created++;
            }
        }

        $this->command->info('Seeded ' . count($owners) . " sample owners, {$pets->count()} pets, and {$created} appointments across " . $services->count() . ' active services.');
    }
}
