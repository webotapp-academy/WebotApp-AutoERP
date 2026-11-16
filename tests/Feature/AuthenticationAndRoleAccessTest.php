<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationAndRoleAccessTest extends TestCase
{
    /**
     * Test demo user logins with credentials and quick-login.
     */
    public function test_all_demo_users_can_authenticate_and_access_dashboard(): void
    {
        $demoUsers = [
            'admin@automobile.com' => 'admin',
            'manager@automobile.com' => 'manager',
            'advisor@automobile.com' => 'service_advisor',
            'mechanic@automobile.com' => 'mechanic',
            'inventory@automobile.com' => 'inventory_manager',
            'accountant@automobile.com' => 'accountant',
        ];

        foreach ($demoUsers as $email => $roleName) {
            $user = User::where('email', $email)->first();
            $this->assertNotNull($user, "Demo user {$email} must exist in seeded database.");

            // Standard login POST
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'password',
            ]);
            $response->assertRedirect('/dashboard');
            $this->assertAuthenticatedAs($user);

            // Access protected dashboard
            $dashResponse = $this->get('/dashboard');
            $dashResponse->assertStatus(200);

            // Logout
            $this->post('/logout');
            $this->assertGuest();
        }
    }

    public function test_quick_one_click_demo_login_works(): void
    {
        $response = $this->get(route('quick-login', 'admin@automobile.com'));
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_key_erp_modules_render_successfully(): void
    {
        $admin = User::where('email', 'admin@automobile.com')->first();
        $this->actingAs($admin);

        $routesToTest = [
            'dashboard',
            'workshop.board',
            'customers.index',
            'vehicles.index',
            'appointments.index',
            'inspections.index',
            'job-cards.index',
            'estimates.index',
            'inventory.index',
            'inventory.ledger',
            'invoices.index',
            'payments.index',
            'mechanics.index',
            'services.index',
            'packages.index',
            'quality.index',
            'suppliers.index',
            'purchases.index',
            'insurance.index',
            'expenses.index',
            'crm.reminders',
            'reports.index',
            'users.index',
            'settings.index',
        ];

        foreach ($routesToTest as $routeName) {
            $response = $this->get(route($routeName));
            $this->assertEquals(200, $response->getStatusCode(), "Route {$routeName} failed to return HTTP 200");
        }
    }
}
