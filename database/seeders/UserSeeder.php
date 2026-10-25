<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(1)->withRoles('user', 'super_admin')->create([
            'username' => 'super_admin',
        ]); // kreiranje jednog super_admin korisnika

        User::factory()->count(20)->withRoles('user')->create(); // kreiranje 20 korisnika sa ulogom 'user'
    }
}
