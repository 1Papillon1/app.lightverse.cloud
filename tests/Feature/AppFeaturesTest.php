<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// RefreshDatabase radi nad SQLite bazom u memoriji (phpunit.xml), prava MySQL baza se ne dira
class AppFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(); // datoteke se ne zapisuju na pravi disk

        Role::create(['name' => Role::USER]);
        Role::create(['name' => Role::SUPER_ADMIN]);
    }

    public function test_register_page_shows_disabled_message(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Registration is disabled');
    }

    public function test_login_with_remember_me_sets_remember_token(): void
    {
        $user = User::factory()->withRoles(Role::USER)->create(['username' => 'tester']);

        $this->post('/login', ['username' => 'tester', 'password' => 'password', 'remember' => 'on'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_upload_of_disallowed_file_type_is_rejected(): void
    {
        $user = User::factory()->withRoles(Role::USER)->create();

        $this->actingAs($user)
            ->post('/files', ['file' => UploadedFile::fake()->create('setup.exe', 10, 'application/x-msdownload')])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, $user->files()->count());
    }

    public function test_upload_of_allowed_file_is_stored_and_logged(): void
    {
        Log::spy();
        $user = User::factory()->withRoles(Role::USER)->create();

        $this->actingAs($user)
            ->post('/files', ['file' => UploadedFile::fake()->create('report.pdf', 50)])
            ->assertSessionHasNoErrors();

        $file = $user->files()->first();
        $this->assertSame('report.pdf', $file->name);
        Storage::assertExists($file->storage_path);
        Log::shouldHaveReceived('info')->with('File uploaded', ['file_id' => $file->id, 'user_id' => $user->id]);
    }

    public function test_owner_can_delete_own_file(): void
    {
        $user = User::factory()->withRoles(Role::USER)->create();
        $file = File::factory()->for($user)->create();

        $this->actingAs($user)->delete("/files/{$file->id}")->assertSessionHas('success');

        $this->assertModelMissing($file);
        Storage::assertMissing($file->storage_path);
    }

    public function test_super_admin_can_delete_another_users_file(): void
    {
        $admin = User::factory()->withRoles(Role::USER, Role::SUPER_ADMIN)->create();
        $file = File::factory()->for(User::factory()->withRoles(Role::USER))->create();

        $this->actingAs($admin)->delete("/files/{$file->id}")->assertSessionHas('success');

        $this->assertModelMissing($file);
    }

    public function test_profile_is_updated_with_patch_and_validated(): void
    {
        $user = User::factory()->withRoles(Role::USER)->create();

        $this->actingAs($user)
            ->patch('/profile', ['first_name' => 'Ana', 'last_name' => 'Horvat', 'address' => 'Ilica 1'])
            ->assertSessionHas('success');
        $this->assertSame('Ana', $user->fresh()->first_name);

        $this->actingAs($user)
            ->patch('/profile', ['first_name' => '', 'last_name' => 'Horvat'])
            ->assertSessionHasErrors('first_name');

        // stara POST ruta više ne postoji
        $this->actingAs($user)->post('/profile', ['first_name' => 'X', 'last_name' => 'Y'])->assertStatus(405);
    }
}
