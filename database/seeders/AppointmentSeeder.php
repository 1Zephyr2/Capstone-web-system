<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Wipe old appointments only (users, pets, services untouched) ──
        Schema::disableForeignKeyConstraints();
        Appointment::truncate();
        Schema::enableForeignKeyConstraints();

        // ── 2. Make sure we have some owners + pets to attach appointments to ──
        $owners = User::where('role', 'owner')->with('pets')->get();

        if ($owners->isEmpty() || $owners->sum(fn($o) => $o->pets->count()) === 0) {
            $dummyOwners = [
                ['name' => 'Shamaimah Reyes', 'email' => 'shamaimah@example.com'],
                ['name' => 'Medge Villanueva', 'email' => 'medge@example.com'],
                ['name' => 'Carlos Dizon',     'email' => 'carlos@example.com'],
                ['name' => 'Angela Cruz',      'email' => 'angela@example.com'],
            ];

            $dummyPets = [
                ['name' => 'Max',   'type' => 'dog', 'breed' => 'Golden Retriever', 'age' => 3],
                ['name' => 'Luna',  'type' => 'cat', 'breed' => 'Persian Cat',      'age' => 2],
                ['name' => 'Bruno', 'type' => 'dog', 'breed' => 'Shih Tzu',         'age' => 4],
                ['name' => 'Milo',  'type' => 'dog', 'breed' => 'Chihuahua',        'age' => 1],
                ['name' => 'Coco',  'type' => 'cat', 'breed' => 'Siamese',          'age' => 5],
            ];

            foreach ($dummyOwners as $i => $data) {
                $owner = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'name'     => $data['name'],
                        'password' => Hash::make('password'),
                        'role'     => 'owner',
                    ]
                );

                $petData = $dummyPets[$i % count($dummyPets)];
                Pet::firstOrCreate(
                    ['user_id' => $owner->id, 'name' => $petData['name']],
                    $petData
                );
            }

            $owners = User::where('role', 'owner')->with('pets')->get();
        }

        $pets = Pet::with('user')->get();

        if ($pets->isEmpty()) {
            $this->command->warn('No pets available — skipping appointment seeding.');
            return;
        }

        // ── 3. Grab active services to assign realistically ──
        $services = Service::active()->get();

        if ($services->isEmpty()) {
            $this->command->warn('No services found — run the services table seeder/migration first.');
            return;
        }

        $notesPool = [
            'Nervous around strangers, please be gentle.',
            'Check ears for infection.',
            'Prefers a quiet room.',
            'Allergic to certain shampoos — use hypoallergenic.',
            'First time grooming, may be anxious.',
            null, null, null, // some appointments have no notes
        ];

        // ── 4. Build a spread of appointments: past, today, future — mixed statuses ──
        $statuses = [
            Appointment::STATUS_PENDING,
            Appointment::STATUS_APPROVED,
            Appointment::STATUS_COMPLETED,
            Appointment::STATUS_REJECTED,
            Appointment::STATUS_CANCELLED,
        ];

        $hours = array_keys(Appointment::CLINIC_HOURS);
        $dayOffsets = [-6, -4, -2, -1, 0, 0, 1, 1, 2, 3, 5, 7]; // relative to today
        $created = 0;

        foreach ($dayOffsets as $i => $offset) {
            $pet     = $pets->random();
            $service = $services->random();
            $hour    = $hours[array_rand($hours)];
            $status  = $offset < 0
                ? collect([Appointment::STATUS_COMPLETED, Appointment::STATUS_CANCELLED, Appointment::STATUS_REJECTED])->random()
                : ($offset === 0
                    ? collect([Appointment::STATUS_APPROVED, Appointment::STATUS_PENDING])->random()
                    : collect($statuses)->random());

            $date = now()->addDays($offset)->setTimeFromTimeString($hour . ':00');

            // Avoid double-booking the same pet at the exact same slot in this seed run
            $exists = Appointment::where('pet_id', $pet->id)
                ->where('appointment_date', $date)
                ->exists();
            if ($exists) {
                continue;
            }

            Appointment::create([
                'user_id'          => $pet->user_id,
                'pet_id'           => $pet->id,
                'service_id'       => $service->id,
                'appointment_date' => $date,
                'status'           => $status,
                'notes'            => $notesPool[array_rand($notesPool)],
                'rejection_reason' => $status === Appointment::STATUS_REJECTED ? 'Slot unavailable, please reschedule.' : null,
            ]);

            $created++;
        }

        $this->command->info("Seeded {$created} dummy appointments.");
    }
}