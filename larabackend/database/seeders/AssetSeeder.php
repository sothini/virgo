<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Seed assets and ensure users exist for association.
     */
    public function run(): void
    {
        if (User::count() === 0) {
            User::factory()->count(2)->create();
        }

        Asset::factory()->count(5)->create();
    }
}

