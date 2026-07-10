<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Create Admin User
        User::firstOrCreate(
            ["email" => "admin@furcare.com"],
            [
                "name" => "Furcare Admin",
                "password" => Hash::make("password"),
                "role" => "admin",
            ]
        );

        // 1. Create Staff User
        User::firstOrCreate(
            ["email" => "staff@furcare.com"],
            [
                "name" => "Furcare Staff",
                "password" => Hash::make("password"),
                "role" => "staff",
            ]
        );

        // 2. Create Pet Owner User
        $owner = User::firstOrCreate(
            ["email" => "owner@furcare.com"],
            [
                "name" => "John Doe",
                "password" => Hash::make("password"),
                "role" => "owner",
            ]
        );

        // 3. Create Pets for Owner
        Pet::firstOrCreate(
            ["user_id" => $owner->id, "name" => "Max"],
            [
                "breed" => "Golden Retriever",
                "age" => 3,
                "special_notes" => "Likes belly rubs.",
            ]
        );

        Pet::firstOrCreate(
            ["user_id" => $owner->id, "name" => "Luna"],
            [
                "breed" => "Persian Cat",
                "age" => 2,
                "special_notes" => "Very quiet.",
            ]
        );

        // 4. Seed dummy appointments (wipes old ones, uses the pets/services above)
        $this->call([
            AppointmentSeeder::class,
        ]);
    }
}