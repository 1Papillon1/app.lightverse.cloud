<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     public function definition(): array
    {
        $extension = fake()->randomElement([
            'pdf', 'docx', 'xlsx', 'pptx', 'jpg', 'png', 'zip', 'txt', 'mp3', 'mp4',
        ]);
 
        // Storage path kao putanja za sve spremljeno određenog usera - u ovome slučaju datoteka
        return [
            'user_id'       => User::factory(),
            'extension'     => $extension,
            'original_name' => fake()->slug(2) . '.' . $extension,
            'storage_path'  => fn (array $attributes) => 'users/' . $attributes['user_id'] . '/'
                                . Str::random(40) . '.' . $attributes['extension'],
            'size'          => fake()->numberBetween(10, 500), // KB
        ];
    }

    // Kreiraj lažni dokument. Pravi dokument u storage-u, ali se ne može koristiti - samo radi testiranja
     public function configure(): static
    {
        // 
        return $this->afterCreating(function (File $file) {
            Storage::put($file->storage_path, str_repeat('0', $file->size * 1024));
        });
    }
}
