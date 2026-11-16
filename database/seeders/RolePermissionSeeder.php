<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Administrator', 'slug' => 'super_admin', 'description' => 'Full system access', 'is_system' => true],
            ['name' => 'Workshop Manager', 'slug' => 'workshop_manager', 'description' => 'Oversees workshop operations, assignments and approvals', 'is_system' => true],
            ['name' => 'Service Advisor', 'slug' => 'service_advisor', 'description' => 'Customer check-in, job cards, estimates and customer communication', 'is_system' => true],
            ['name' => 'Mechanic / Technician', 'slug' => 'mechanic', 'description' => 'Job execution, repair checklists, parts requisition and time tracking', 'is_system' => true],
            ['name' => 'Inventory Manager', 'slug' => 'inventory_manager', 'description' => 'Spare parts, stock movements, purchase orders and supplier management', 'is_system' => true],
            ['name' => 'Accountant / Cashier', 'slug' => 'accountant', 'description' => 'Invoicing, payment collection, expenses and financial reports', 'is_system' => true],
            ['name' => 'Customer', 'slug' => 'customer', 'description' => 'Customer portal access for service tracking and approvals', 'is_system' => true],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        $permissions = [
            // Dashboard
            ['name' => 'View Dashboard', 'slug' => 'dashboard.view', 'group' => 'dashboard'],
            // Customers
            ['name' => 'View Customers', 'slug' => 'customers.view', 'group' => 'customers'],
            ['name' => 'Create Customers', 'slug' => 'customers.create', 'group' => 'customers'],
            ['name' => 'Edit Customers', 'slug' => 'customers.edit', 'group' => 'customers'],
            // Vehicles
            ['name' => 'View Vehicles', 'slug' => 'vehicles.view', 'group' => 'vehicles'],
            ['name' => 'Create Vehicles', 'slug' => 'vehicles.create', 'group' => 'vehicles'],
            ['name' => 'Edit Vehicles', 'slug' => 'vehicles.edit', 'group' => 'vehicles'],
            // Appointments
            ['name' => 'View Appointments', 'slug' => 'appointments.view', 'group' => 'appointments'],
            ['name' => 'Manage Appointments', 'slug' => 'appointments.manage', 'group' => 'appointments'],
            // Inspections
            ['name' => 'Perform Inspections', 'slug' => 'inspections.manage', 'group' => 'inspections'],
            // Job Cards
            ['name' => 'View Job Cards', 'slug' => 'job_cards.view', 'group' => 'job_cards'],
            ['name' => 'Create Job Cards', 'slug' => 'job_cards.create', 'group' => 'job_cards'],
            ['name' => 'Edit Job Cards', 'slug' => 'job_cards.edit', 'group' => 'job_cards'],
            ['name' => 'Manage Job Cards Workflow', 'slug' => 'job_cards.workflow', 'group' => 'job_cards'],
            // Estimates
            ['name' => 'View Estimates', 'slug' => 'estimates.view', 'group' => 'estimates'],
            ['name' => 'Manage Estimates', 'slug' => 'estimates.manage', 'group' => 'estimates'],
            // Inventory
            ['name' => 'View Inventory', 'slug' => 'inventory.view', 'group' => 'inventory'],
            ['name' => 'Manage Stock', 'slug' => 'inventory.manage', 'group' => 'inventory'],
            ['name' => 'Manage Purchases', 'slug' => 'purchases.manage', 'group' => 'purchases'],
            // Invoices & Billing
            ['name' => 'View Invoices', 'slug' => 'invoices.view', 'group' => 'billing'],
            ['name' => 'Create Invoices', 'slug' => 'invoices.create', 'group' => 'billing'],
            ['name' => 'Collect Payments', 'slug' => 'payments.collect', 'group' => 'billing'],
            // Quality Control
            ['name' => 'Quality Control', 'slug' => 'quality.manage', 'group' => 'quality'],
            // Insurance
            ['name' => 'Manage Insurance Claims', 'slug' => 'insurance.manage', 'group' => 'insurance'],
            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'group' => 'reports'],
            // Settings
            ['name' => 'System Settings', 'slug' => 'settings.manage', 'group' => 'settings'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        // Assign all permissions to super_admin and manager
        $allPermissions = Permission::all();
        $adminRole = Role::where('slug', 'super_admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
        }

        $mgrRole = Role::where('slug', 'workshop_manager')->first();
        if ($mgrRole) {
            $mgrRole->permissions()->sync($allPermissions->pluck('id'));
        }

        $advisorRole = Role::where('slug', 'service_advisor')->first();
        if ($advisorRole) {
            $advisorPerms = Permission::whereIn('group', ['dashboard', 'customers', 'vehicles', 'appointments', 'inspections', 'job_cards', 'estimates', 'billing'])->get();
            $advisorRole->permissions()->sync($advisorPerms->pluck('id'));
        }

        $mechanicRole = Role::where('slug', 'mechanic')->first();
        if ($mechanicRole) {
            $mechPerms = Permission::whereIn('slug', ['dashboard.view', 'job_cards.view', 'inspections.manage', 'quality.manage'])->get();
            $mechanicRole->permissions()->sync($mechPerms->pluck('id'));
        }

        $invRole = Role::where('slug', 'inventory_manager')->first();
        if ($invRole) {
            $invPerms = Permission::whereIn('group', ['dashboard', 'inventory', 'purchases'])->get();
            $invRole->permissions()->sync($invPerms->pluck('id'));
        }

        $acctRole = Role::where('slug', 'accountant')->first();
        if ($acctRole) {
            $acctPerms = Permission::whereIn('group', ['dashboard', 'billing', 'reports'])->get();
            $acctRole->permissions()->sync($acctPerms->pluck('id'));
        }
    }
}

