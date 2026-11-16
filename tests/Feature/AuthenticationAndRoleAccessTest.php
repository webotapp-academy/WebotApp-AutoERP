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

    public function test_each_role_sees_tailored_role_specific_dashboard(): void
    {
        // 1. Mechanic
        $mechanic = User::where('email', 'mechanic@automobile.com')->first();
        $this->actingAs($mechanic);
        $resMech = $this->get('/dashboard');
        $resMech->assertStatus(200);
        $resMech->assertSee('Mechanic Workbench');
        $resMech->assertSee('My Assigned Repair Work Orders');
        $resMech->assertDontSee('GST Output Tax Breakdown');

        // 2. Inventory Manager
        $inventory = User::where('email', 'inventory@automobile.com')->first();
        $this->actingAs($inventory);
        $resInv = $this->get('/dashboard');
        $resInv->assertStatus(200);
        $resInv->assertSee('Inventory Hub');
        $resInv->assertSee('Critical Low Stock Items');
        $resInv->assertDontSee('Mechanic Workbench');

        // 3. Accountant
        $accountant = User::where('email', 'accountant@automobile.com')->first();
        $this->actingAs($accountant);
        $resAcct = $this->get('/dashboard');
        $resAcct->assertStatus(200);
        $resAcct->assertSee('Financial Dashboard');
        $resAcct->assertSee('GST Output Tax Breakdown');
        $resAcct->assertDontSee('Mechanic Workbench');

        // 4. Service Advisor
        $advisor = User::where('email', 'advisor@automobile.com')->first();
        $this->actingAs($advisor);
        $resAdv = $this->get('/dashboard');
        $resAdv->assertStatus(200);
        $resAdv->assertSee('Service Advisory Desk');
        $resAdv->assertSee("Today's Booked Appointments", false);
        $resAdv->assertDontSee('Critical Low Stock Items');

        // 5. Admin
        $admin = User::where('email', 'admin@automobile.com')->first();
        $this->actingAs($admin);
        $resAdmin = $this->get('/dashboard');
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Workshop Operations Executive Dashboard');
        $resAdmin->assertSee('Service Bay Live Floor');
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

