<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_user_cannot_access_users_list()
    {
        $response = $this->get('/users');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function regular_user_cannot_access_users_list()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)->get('/users');
        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_view_users_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/users');
        $response->assertSuccessful();
        $response->assertViewIs('users.index');
    }

    /** @test */
    public function admin_can_access_create_user_form()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/users/create');
        $response->assertSuccessful();
        $response->assertViewIs('users.create');
    }

    /** @test */
    public function regular_user_cannot_create_user()
    {
        $user = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user)
            ->post('/users', [
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password!@#',
                'password_confirmation' => 'password!@#',
                'role' => 'user',
                'status' => 'active'
            ]);
        
        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_create_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
            ->post('/users', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
                'status' => 'active'
            ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'role' => 'user',
            'status' => 'active'
        ]);
    }

    /** @test */
    public function admin_cannot_create_user_with_duplicate_email()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        
        $response = $this->actingAs($admin)
            ->post('/users', [
                'name' => 'John Doe',
                'email' => 'existing@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
                'status' => 'active'
            ]);
        
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function password_must_be_minimum_8_characters()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
            ->post('/users', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'passs',
                'password_confirmation' => 'passs',
                'role' => 'user',
                'status' => 'active'
            ]);
        
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function password_confirmation_must_match()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)
            ->post('/users', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'different',
                'role' => 'user',
                'status' => 'active'
            ]);
        
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function admin_can_view_user_details()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $response = $this->actingAs($admin)->get("/users/{$user->id}");
        $response->assertSuccessful();
        $response->assertViewIs('users.show');
        $response->assertViewHas('user', $user);
    }

    /** @test */
    public function user_can_view_own_profile()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get("/users/{$user->id}");
        $response->assertSuccessful();
    }

    /** @test */
    public function user_cannot_view_other_user_profile()
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);
        
        $response = $this->actingAs($user1)->get("/users/{$user2->id}");
        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_edit_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Old Name']);
        
        $response = $this->actingAs($admin)
            ->put("/users/{$user->id}", [
                'name' => 'New Name',
                'email' => $user->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => 'admin',
                'status' => 'active'
            ]);
        
        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('admin', $user->role);
    }

    /** @test */
    public function admin_can_update_password_on_edit()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $this->actingAs($admin)
            ->put("/users/{$user->id}", [
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'role' => $user->role,
                'status' => $user->status
            ]);
        
        // Try to login with new password
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'newpassword123'
        ]);
        
        $this->assertAuthenticated();
    }

    /** @test */
    public function admin_can_deactivate_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['status' => 'active']);
        
        $response = $this->actingAs($admin)
            ->post("/users/{$user->id}/deactivate");
        
        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('inactive', $user->status);
    }

    /** @test */
    public function admin_can_activate_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['status' => 'inactive']);
        
        $response = $this->actingAs($admin)
            ->post("/users/{$user->id}/activate");
        
        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('active', $user->status);
    }

    /** @test */
    public function admin_can_soft_delete_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $response = $this->actingAs($admin)
            ->delete("/users/{$user->id}");
        
        $response->assertRedirect();
        $this->assertSoftDeleted($user);
    }

    /** @test */
    public function admin_can_view_audit_logs()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        // Create some audit log entry
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'actor_id' => $admin->id,
            'action' => 'created',
            'model_type' => User::class,
            'model_id' => $user->id,
            'changes' => ['name' => $user->name]
        ]);
        
        $response = $this->actingAs($admin)
            ->get("/users/{$user->id}/audit-logs");
        
        $response->assertSuccessful();
        $response->assertViewIs('users.audit-logs');
    }

    /** @test */
    public function audit_log_is_created_on_user_creation()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $this->actingAs($admin)
            ->post('/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'user',
                'status' => 'active'
            ]);
        
        $user = User::where('email', 'test@example.com')->first();
        
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'created',
            'model_type' => User::class,
            'model_id' => $user->id
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password')
        ]);
        
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password'
        ]);
        
        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    /** @test */
    public function user_cannot_login_with_invalid_password()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password')
        ]);
        
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password'
        ]);
        
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/logout');
        
        $response->assertRedirect();
        $this->assertGuest();
    }

    /** @test */
    public function user_model_has_isAdmin_method()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        $regularUser = User::factory()->create(['role' => 'user']);
        
        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($regularUser->isAdmin());
    }

    /** @test */
    public function user_model_has_isActive_method()
    {
        $activeUser = User::factory()->create(['status' => 'active']);
        $inactiveUser = User::factory()->create(['status' => 'inactive']);
        
        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($inactiveUser->isActive());
    }
}
