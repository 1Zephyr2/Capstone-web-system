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
        User::create([
            "name" => "Furcare Admin",
            "email" => "admin@furcare.com",
            "password" => Hash::make("password"),
            "role" => "admin",
        ]);

        // 1. Create Staff User
        User::create([
            "name" => "Furcare Staff",
            "email" => "staff@furcare.com",
            "password" => Hash::make("password"),
            "role" => "staff",
        ]);

        // 2. Create Pet Owner User
        $owner = User::create([
            "name" => "John Doe",
            "email" => "owner@furcare.com",
            "password" => Hash::make("password"),
            "role" => "owner",
        ]);

        // 3. Create Pets for Owner
        Pet::create([
            "user_id" => $owner->id,
            "name" => "Max",
            "breed" => "Golden Retriever",
            "age" => 3,
            "special_notes" => "Likes belly rubs.",
        ]);

        Pet::create([
            "user_id" => $owner->id,
            "name" => "Luna",
            "breed" => "Persian Cat",
            "age" => 2,
            "special_notes" => "Very quiet.",
        ]);
    }
}
