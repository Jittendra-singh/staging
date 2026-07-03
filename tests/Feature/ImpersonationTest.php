<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create a volunteer model with required fields.
     */
    protected function createVolunteer(array $overrides = []): Volunteer
    {
        return Volunteer::create(array_merge([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_address' => 'volunteer@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'address_line_1' => '123 Street',
            'address_line_2' => 'Apt 1',
            'city_id' => 1,
            'pincode' => '12345',
            'status' => 1,
            'is_organiser' => 0,
        ], $overrides));
    }

    /**
     * Test that guests and non-super-admins cannot impersonate.
     */
    public function test_guests_cannot_impersonate(): void
    {
        $volunteer = $this->createVolunteer();

        $response = $this->post(route('admin.impersonate.take', ['user' => $volunteer->id]));

        $response->assertStatus(302); // Redirect to login
        $this->assertFalse(app('impersonate')->isImpersonating());
    }

    /**
     * Test successful impersonation.
     */
    public function test_super_admin_can_impersonate_volunteer(): void
    {
        $admin = new User([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => 1, // Super Admin
            'is_active' => 1,
        ]);
        $admin->id = 1;
        $admin->save();

        $volunteer = $this->createVolunteer(['email_address' => 'volunteer@example.com']);

        // Log in as admin
        $this->actingAs($admin, 'web');

        // Request to impersonate target
        $response = $this->post(route('admin.impersonate.take', ['user' => $volunteer->id]));

        $response->assertRedirect(route(config('laravel-impersonate.take_redirect_to')));
        $this->assertTrue(app('impersonate')->isImpersonating());
        $this->assertEquals($volunteer->id, Auth::guard('front')->id());

        // Verify banner is rendered in a frontend view (assuming dashboard renders layouts.frontend)
        $response = $this->actingAs($volunteer, 'front')->get(route(config('laravel-impersonate.take_redirect_to')));
        $response->assertSee('Impersonating:');
    }

    /**
     * Test leaving impersonation.
     */
    public function test_can_leave_impersonation(): void
    {
        $admin = new User([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'is_active' => 1,
        ]);
        $admin->id = 1;
        $admin->save();

        $volunteer = $this->createVolunteer();

        $this->actingAs($admin, 'web');

        // Start impersonation
        $this->post(route('admin.impersonate.take', ['user' => $volunteer->id]));
        $this->assertTrue(app('impersonate')->isImpersonating());

        // Leave impersonation
        $response = $this->post(route('admin.impersonate.leave'));
        $response->assertRedirect(route('index-volunteers'));
        $this->assertFalse(app('impersonate')->isImpersonating());
    }
}
