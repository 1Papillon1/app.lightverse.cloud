<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// RefreshDatabase radi nad SQLite bazom u memoriji (phpunit.xml), prava MySQL baza se ne dira
class AppAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(); // datoteke se ne zapisuju na pravi disk

        Role::create(['name' => 'user']);
        Role::create(['name' => 'super_admin']);
    }

    public function test_user_cannot_access_admin_pages(): void
    {
        $user = User::factory()->withRoles('user')->create();

        $this->actingAs($user)->get('/admin/files')->assertForbidden();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_user_cannot_download_another_users_file(): void
    {
        $user = User::factory()->withRoles('user')->create();
        $file = File::factory()->for(User::factory()->withRoles('user'))->create();

        $this->actingAs($user)->get("/files/{$file->id}/download")->assertForbidden();
    }

    public function test_upload_over_storage_limit_is_rejected(): void
    {
        $user = User::factory()->withRoles('user')->create(['storage_limit' => 100]); // 100 KB

        $this->actingAs($user)
            ->post('/files', ['file' => UploadedFile::fake()->create('big.pdf', 200)]) // 200 KB
            ->assertSessionHasErrors(['file' => 'Not enough free storage space.']);

        $this->assertSame(0, $user->files()->count());
    }

    public function test_files_list_shows_five_files_per_page(): void
    {
        $user = User::factory()->withRoles('user')->create();
        File::factory()->count(7)->for($user)->create();

        $this->actingAs($user)->get('/files')
            ->assertOk()
            ->assertViewHas('files', fn ($files) => $files->count() === 5 && $files->total() === 7);

        $this->actingAs($user)->get('/files?page=2')
            ->assertViewHas('files', fn ($files) => $files->count() === 2);
    }

    public function test_super_admin_without_user_role_cannot_access_user_pages(): void
    {
        $admin = User::factory()->withRoles(Role::SUPER_ADMIN)->create();
        $file = File::factory()->for(User::factory()->withRoles(Role::USER))->create();

        $this->actingAs($admin)->get('/files')->assertForbidden();
        $this->actingAs($admin)->get('/profile')->assertForbidden();

        // download tuđe datoteke i dalje radi (FilePolicy), jer je ruta izvan role:user grupe
        $this->actingAs($admin)->get("/files/{$file->id}/download")->assertOk();
    }

    public function test_non_numeric_file_id_returns_404(): void
    {
        $user = User::factory()->withRoles(Role::USER)->create();

        $this->actingAs($user)->get('/files/abc/download')->assertNotFound();
        $this->actingAs($user)->delete('/files/abc')->assertNotFound();
    }

    public function test_login_is_throttled_after_five_attempts(): void
    {
        User::factory()->withRoles('user')->create(['username' => 'tester']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['username' => 'tester', 'password' => 'wrong'])->assertRedirect();
        }

        $this->post('/login', ['username' => 'tester', 'password' => 'wrong'])->assertStatus(429);
    }
}
