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
          // Prvi korisnik: uloge user i super_admin
        User::factory()->withRoles('user', 'super_admin')->create([
            'username' => 'super_admin',
            'password' => 'admin123',
        ]);
 
        // Drugi korisnik: samo uloga user
        User::factory()->withRoles('user')->create([
            'username' => 'user',
            'password' => 'user123',
        ]);

        User::factory()->count(20)->withRoles('user')->create(); // kreiranje 20 korisnika sa ulogom 'user'
    }
}
