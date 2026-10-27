<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // generiranje različitog broj dokumenata za svakog usera u bazi (od 0 do 8)
        User::whereHas('roles', fn ($q) => $q->where('name', 'user'))
            ->each(function (User $user)) {
                File::factory()
                ->count(rand(0, 8))
                ->for($user)
                ->create();
        }
    }
}
