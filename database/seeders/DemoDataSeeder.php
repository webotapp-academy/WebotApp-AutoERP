<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;
use App\Models\ServiceBay;
use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\VehicleType;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\Vehicle;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\ServicePackageItem;
use App\Models\SparePartCategory;
use App\Models\SparePart;
use App\Models\Supplier;
use App\Models\StockLedger;
use App\Models\Appointment;
use App\Models\VehicleInspection;
use App\Models\JobCard;
use App\Models\JobCardService;
use App\Models\JobCardPart;
use App\Models\RepairEstimate;
use App\Models\RepairEstimateItem;
use App\Models\QualityInspection;
use App\Models\InsuranceCompany;
use App\Models\InsuranceClaim;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\ServiceReminder;
use App\Models\CustomerFeedback;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\SystemSetting;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Company
        $company = Company::create([
            'name' => 'ABC Automobile Care & Service Centre',
            'legal_name' => 'ABC Auto Care Solutions Pvt. Ltd.',
            'registration_number' => 'U50200MH2021PTC368945',
            'gstin' => '27AABCU9603R1ZM',
            'pan_number' => 'AABCU9603R',
            'email' => 'info@abcautocare.com',
            'phone' => '+91 98200 12345',
            'website' => 'https://www.abcautocare.com',
            'address' => 'Plot 42, Sector 18, Automotive Zone',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'postal_code' => '400705',
            'country' => 'India',
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'invoice_prefix' => 'ABC-',
            'settings' => [
                'tax_type' => 'GST',
                'cgst_default' => 9.00,
                'sgst_default' => 9.00,
                'igst_default' => 18.00,
                'allow_negative_stock' => false,
            ]
        ]);

        // 2. Branches
        $mainBranch = Branch::create([
            'company_id' => $company->id,
            'name' => 'Downtown Central Workshop',
            'code' => 'DOWNTOWN-01',
            'phone' => '+91 98200 12346',
            'email' => 'downtown@abcautocare.com',
            'address' => 'Plot 42, Sector 18, Industrial Estate',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'postal_code' => '400705',
            'is_main' => true,
            'is_active' => true,
        ]);

        $expressBranch = Branch::create([
            'company_id' => $company->id,
            'name' => 'Highway Express Hub',
            'code' => 'HIGHWAY-02',
            'phone' => '+91 98200 12347',
            'email' => 'highway@abcautocare.com',
            'address' => 'Service Road, Near Toll Plaza, NH-48',
            'city' => 'Navi Mumbai',
            'state' => 'Maharashtra',
            'postal_code' => '410210',
            'is_main' => false,
            'is_active' => true,
        ]);

        // Departments
        Department::create(['branch_id' => $mainBranch->id, 'name' => 'Mechanical & Periodic', 'description' => 'General maintenance and powertrain']);
        Department::create(['branch_id' => $mainBranch->id, 'name' => 'Body Shop & Paint', 'description' => 'Denting, painting and chassis alignment']);
        Department::create(['branch_id' => $mainBranch->id, 'name' => 'EV Diagnostics & Electrical', 'description' => 'High voltage systems and electronic wiring']);
        Department::create(['branch_id' => $mainBranch->id, 'name' => 'Quick Lube & Detailing', 'description' => 'Express oil changes and interior sanitization']);

        // 3. Service Bays (5 Bays)
        $bays = [
            ['name' => 'Bay 1 - Heavy Mechanical & Periodic', 'code' => 'BAY-01', 'bay_type' => 'General', 'status' => 'occupied'],
            ['name' => 'Bay 2 - 3D Wheel Alignment & Balancing', 'code' => 'BAY-02', 'bay_type' => 'Wheel Alignment', 'status' => 'available'],
            ['name' => 'Bay 3 - Express Lube & Quick Service', 'code' => 'BAY-03', 'bay_type' => 'Quick Lube', 'status' => 'occupied'],
            ['name' => 'Bay 4 - EV Diagnostics & High Voltage', 'code' => 'BAY-04', 'bay_type' => 'EV Bay', 'status' => 'available'],
            ['name' => 'Bay 5 - Down-Draft Paint Booth & Oven', 'code' => 'BAY-05', 'bay_type' => 'Paint Booth', 'status' => 'available'],
        ];

        $serviceBays = [];
        foreach ($bays as $bay) {
            $serviceBays[] = ServiceBay::create(array_merge($bay, ['branch_id' => $mainBranch->id]));
        }

        // 4. Roles
        $adminRole = Role::where('slug', 'super_admin')->first();
        $managerRole = Role::where('slug', 'workshop_manager')->first();
        $advisorRole = Role::where('slug', 'service_advisor')->first();
        $mechanicRole = Role::where('slug', 'mechanic')->first();
        $invRole = Role::where('slug', 'inventory_manager')->first();
        $acctRole = Role::where('slug', 'accountant')->first();

        // 5. Users & Employees (10 Employees)
        $usersData = [
            [
                'name' => 'Rajesh Sharma (Admin)',
                'email' => 'admin@automobile.com',
                'phone' => '+91 98111 00001',
                'role_id' => $adminRole->id,
                'designation' => 'Managing Director & Chief Engineer',
                'salary' => 95000,
                'hourly_rate' => 600,
            ],
            [
                'name' => 'Vikram Patil (Manager)',
                'email' => 'manager@automobile.com',
                'phone' => '+91 98111 00002',
                'role_id' => $managerRole->id,
                'designation' => 'Workshop General Manager',
                'salary' => 65000,
                'hourly_rate' => 450,
            ],
            [
                'name' => 'Amit Verma (Lead Advisor)',
                'email' => 'advisor@automobile.com',
                'phone' => '+91 98111 00003',
                'role_id' => $advisorRole->id,
                'designation' => 'Senior Service Advisor',
                'salary' => 42000,
                'hourly_rate' => 280,
            ],
            [
                'name' => 'Pooja Nair (Service Advisor)',
                'email' => 'advisor2@automobile.com',
                'phone' => '+91 98111 00004',
                'role_id' => $advisorRole->id,
                'designation' => 'Customer Relations & Service Advisor',
                'salary' => 38000,
                'hourly_rate' => 250,
            ],
            [
                'name' => 'Suresh Kumar (Lead Mechanic)',
                'email' => 'mechanic@automobile.com',
                'phone' => '+91 98111 00005',
                'role_id' => $mechanicRole->id,
                'designation' => 'Master Technician & Lead Mechanic',
                'salary' => 48000,
                'hourly_rate' => 320,
            ],
            [
                'name' => 'Dinesh Patel (Senior Technician)',
                'email' => 'tech1@automobile.com',
                'phone' => '+91 98111 00006',
                'role_id' => $mechanicRole->id,
                'designation' => 'Powertrain & Engine Specialist',
                'salary' => 36000,
                'hourly_rate' => 240,
            ],
            [
                'name' => 'Sunil Rao (Electrical & EV Tech)',
                'email' => 'tech2@automobile.com',
                'phone' => '+91 98111 00007',
                'role_id' => $mechanicRole->id,
                'designation' => 'Certified High-Voltage EV Technician',
                'salary' => 40000,
                'hourly_rate' => 260,
            ],
            [
                'name' => 'Deepak Yadav (Suspension & Brakes)',
                'email' => 'tech3@automobile.com',
                'phone' => '+91 98111 00008',
                'role_id' => $mechanicRole->id,
                'designation' => 'Chassis & Alignment Specialist',
                'salary' => 32000,
                'hourly_rate' => 220,
            ],
            [
                'name' => 'Manoj Gupta (Inventory Manager)',
                'email' => 'inventory@automobile.com',
                'phone' => '+91 98111 00009',
                'role_id' => $invRole->id,
                'designation' => 'Spare Parts & Stores Manager',
                'salary' => 45000,
                'hourly_rate' => 300,
            ],
            [
                'name' => 'Sneha Kulkarni (Accountant)',
                'email' => 'accountant@automobile.com',
                'phone' => '+91 98111 00010',
                'role_id' => $acctRole->id,
                'designation' => 'Financial Controller & Chief Accountant',
                'salary' => 45000,
                'hourly_rate' => 300,
            ],
        ];

        $users = [];
        $empCode = 101;
        foreach ($usersData as $u) {
            $user = User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => Hash::make('password'),
                'phone' => $u['phone'],
                'company_id' => $company->id,
                'branch_id' => $mainBranch->id,
                'role_id' => $u['role_id'],
                'status' => 'active',
            ]);

            Employee::create([
                'user_id' => $user->id,
                'branch_id' => $mainBranch->id,
                'employee_code' => 'EMP-' . $empCode++,
                'designation' => $u['designation'],
                'department' => 'Workshop Operations',
                'joining_date' => now()->subMonths(18)->toDateString(),
                'salary' => $u['salary'],
                'hourly_rate' => $u['hourly_rate'],
                'commission_rate' => 5.00,
                'status' => 'active',
            ]);

            $users[$u['email']] = $user;
        }

        // 6. Vehicle Types
        $twoWheeler = VehicleType::create(['name' => 'Two-Wheeler (Motorcycle/Scooter)', 'code' => 'two_wheeler']);
        $fourWheeler = VehicleType::create(['name' => 'Four-Wheeler Passenger Car', 'code' => 'passenger_car']);
        $suvType = VehicleType::create(['name' => 'SUV / MUV', 'code' => 'suv']);
        $commercial = VehicleType::create(['name' => 'Light Commercial Vehicle (LCV)', 'code' => 'commercial']);
        $evType = VehicleType::create(['name' => 'Electric Vehicle (EV)', 'code' => 'ev']);

        // 7. Vehicle Brands & 20 Models
        $brands = [
            'Maruti Suzuki' => ['Swift', 'Baleno', 'Brezza', 'Ertiga', 'Dzire'],
            'Hyundai' => ['Creta', 'i20', 'Venue', 'Verna'],
            'Tata Motors' => ['Nexon', 'Harrier', 'Punch', 'Ace Gold', 'Nexon EV'],
            'Honda' => ['City', 'Amaze', 'Activa 6G'],
            'Mahindra' => ['Scorpio-N', 'Thar', 'XUV700', 'Bolero'],
            'Toyota' => ['Innova Crysta', 'Fortuner', 'Urban Cruiser Hyryder'],
            'Royal Enfield' => ['Classic 350', 'Hunter 350', 'Meteor 350'],
            'TVS' => ['Jupiter 125', 'Apache RTR 160'],
        ];

        $models = [];
        foreach ($brands as $brandName => $modelNames) {
            $brand = VehicleBrand::create(['name' => $brandName]);
            foreach ($modelNames as $mName) {
                $typeId = $fourWheeler->id;
                if (in_array($mName, ['Classic 350', 'Hunter 350', 'Meteor 350', 'Activa 6G', 'Jupiter 125', 'Apache RTR 160'])) {
                    $typeId = $twoWheeler->id;
                } elseif (in_array($mName, ['Scorpio-N', 'Thar', 'XUV700', 'Innova Crysta', 'Fortuner', 'Harrier', 'Creta'])) {
                    $typeId = $suvType->id;
                } elseif (in_array($mName, ['Ace Gold', 'Bolero'])) {
                    $typeId = $commercial->id;
                } elseif (in_array($mName, ['Nexon EV'])) {
                    $typeId = $evType->id;
                }

                $model = VehicleModel::create([
                    'brand_id' => $brand->id,
                    'vehicle_type_id' => $typeId,
                    'name' => $mName,
                    'fuel_types' => $mName === 'Nexon EV' ? ['Electric'] : ['Petrol', 'Diesel'],
                ]);
                $models[] = $model;
            }
        }

        // 8. 20 Customers (Mix of Individual, Corporate, Fleet)
        $customersData = [
            ['first_name' => 'Rahul', 'last_name' => 'Deshmukh', 'type' => 'individual', 'phone' => '+91 98210 11111', 'email' => 'rahul.deshmukh@gmail.com', 'city' => 'Mumbai'],
            ['first_name' => 'Ananya', 'last_name' => 'Iyer', 'type' => 'individual', 'phone' => '+91 98210 22222', 'email' => 'ananya.iyer@outlook.com', 'city' => 'Navi Mumbai'],
            ['first_name' => 'Vikrant', 'last_name' => 'Mehta', 'type' => 'individual', 'phone' => '+91 98210 33333', 'email' => 'vikrant.mehta@yahoo.com', 'city' => 'Thane'],
            ['first_name' => 'Priya', 'last_name' => 'Sharma', 'type' => 'individual', 'phone' => '+91 98210 44444', 'email' => 'priya.sharma@gmail.com', 'city' => 'Mumbai'],
            ['first_name' => 'Aditya', 'last_name' => 'Kapoor', 'type' => 'individual', 'phone' => '+91 98210 55555', 'email' => 'aditya.k@gmail.com', 'city' => 'Mumbai'],
            ['first_name' => 'Sanjay', 'last_name' => 'Jadhav', 'type' => 'individual', 'phone' => '+91 98210 66666', 'email' => 'sanjay.j@hotmail.com', 'city' => 'Pune'],
            ['first_name' => 'Kavita', 'last_name' => 'Reddy', 'type' => 'individual', 'phone' => '+91 98210 77777', 'email' => 'kavita.reddy@gmail.com', 'city' => 'Mumbai'],
            ['first_name' => 'Naveen', 'last_name' => 'Chawla', 'type' => 'individual', 'phone' => '+91 98210 88888', 'email' => 'naveen.chawla@gmail.com', 'city' => 'Thane'],
            ['first_name' => 'Sunita', 'last_name' => 'Bhatt', 'type' => 'individual', 'phone' => '+91 98210 99999', 'email' => 'sunita.b@gmail.com', 'city' => 'Mumbai'],
            ['first_name' => 'Manish', 'last_name' => 'Tiwari', 'type' => 'individual', 'phone' => '+91 98211 10001', 'email' => 'manish.t@gmail.com', 'city' => 'Navi Mumbai'],
            ['first_name' => 'Rohan', 'last_name' => 'Kulkarni', 'type' => 'individual', 'phone' => '+91 98211 10002', 'email' => 'rohan.k@gmail.com', 'city' => 'Mumbai'],
            ['first_name' => 'Divya', 'last_name' => 'Pillai', 'type' => 'individual', 'phone' => '+91 98211 10003', 'email' => 'divya.p@gmail.com', 'city' => 'Thane'],
            ['first_name' => 'Apex Logistics', 'last_name' => 'Pvt Ltd', 'type' => 'fleet', 'company_name' => 'Apex Logistics Solutions', 'gstin' => '27AAACA1234D1Z2', 'phone' => '+91 98211 10004', 'email' => 'fleet@apexlogistics.in', 'city' => 'Mumbai'],
            ['first_name' => 'BlueDart Courier', 'last_name' => 'Fleet', 'type' => 'fleet', 'company_name' => 'BlueExpress Regional Fleet', 'gstin' => '27AAACB5678E1Z3', 'phone' => '+91 98211 10005', 'email' => 'dispatch@bluefleet.com', 'city' => 'Navi Mumbai'],
            ['first_name' => 'TechCorp India', 'last_name' => 'Cab Service', 'type' => 'corporate', 'company_name' => 'TechCorp Mobility Pvt Ltd', 'gstin' => '27AAACT9012F1Z4', 'phone' => '+91 98211 10006', 'email' => 'admin@techcorpmobility.com', 'city' => 'Mumbai'],
            ['first_name' => 'Star Cabs', 'last_name' => 'Services', 'type' => 'corporate', 'company_name' => 'Star City Cabs Network', 'gstin' => '27AAACS3456G1Z5', 'phone' => '+91 98211 10007', 'email' => 'fleet@starcabs.in', 'city' => 'Thane'],
            ['first_name' => 'Arun', 'last_name' => 'Pandey', 'type' => 'individual', 'phone' => '+91 98211 10008', 'email' => 'arun.pandey@gmail.com', 'city' => 'Mumbai'],
            ['first_name' => 'Geeta', 'last_name' => 'Menon', 'type' => 'individual', 'phone' => '+91 98211 10009', 'email' => 'geeta.menon@gmail.com', 'city' => 'Navi Mumbai'],
            ['first_name' => 'Kunal', 'last_name' => 'Bakshi', 'type' => 'individual', 'phone' => '+91 98211 10010', 'email' => 'kunal.b@gmail.com', 'city' => 'Mumbai'],
            ['first_name' => 'Zoya', 'last_name' => 'Khan', 'type' => 'individual', 'phone' => '+91 98211 10011', 'email' => 'zoya.k@gmail.com', 'city' => 'Thane'],
        ];

        $customers = [];
        foreach ($customersData as $c) {
            $customers[] = Customer::create([
                'company_id' => $company->id,
                'branch_id' => $mainBranch->id,
                'customer_type' => $c['type'],
                'first_name' => $c['first_name'],
                'last_name' => $c['last_name'],
                'company_name' => $c['company_name'] ?? null,
                'email' => $c['email'],
                'phone' => $c['phone'],
                'gstin' => $c['gstin'] ?? null,
                'address' => 'Sample Address, ' . $c['city'],
                'city' => $c['city'],
                'state' => 'Maharashtra',
                'pincode' => '400001',
                'credit_limit' => $c['type'] === 'fleet' ? 200000 : 25000,
                'outstanding_balance' => 0.00,
                'status' => 'active',
            ]);
        }

        // 9. 30 Vehicles
        $regNumbers = [
            'MH-02-AB-1001', 'MH-03-CD-2002', 'MH-04-EF-3003', 'MH-01-GH-4004', 'MH-43-JK-5005',
            'MH-02-LM-6006', 'MH-03-NP-7007', 'MH-04-QR-8008', 'MH-01-ST-9009', 'MH-43-UV-1010',
            'MH-02-WX-2020', 'MH-03-YZ-3030', 'MH-04-BC-4040', 'MH-01-DE-5050', 'MH-43-FG-6060',
            'MH-02-HI-7070', 'MH-03-JK-8080', 'MH-04-LM-9090', 'MH-01-NO-1111', 'MH-43-PQ-2222',
            'MH-02-RS-3333', 'MH-03-TU-4444', 'MH-04-VW-5555', 'MH-01-XY-6666', 'MH-43-ZA-7777',
            'MH-02-BC-8888', 'MH-03-DE-9999', 'MH-04-FG-1212', 'MH-01-HI-2323', 'MH-43-JK-3434',
        ];

        $vehicles = [];
        for ($i = 0; $i < 30; $i++) {
            $model = $models[$i % count($models)];
            $customer = $customers[$i % count($customers)];
            $vehicles[] = Vehicle::create([
                'customer_id' => $customer->id,
                'branch_id' => $mainBranch->id,
                'vehicle_type_id' => $model->vehicle_type_id,
                'brand_id' => $model->brand_id,
                'model_id' => $model->id,
                'registration_number' => $regNumbers[$i],
                'vin_chassis_number' => 'MA3ER' . rand(10000000, 99999999),
                'engine_number' => 'ENG' . rand(100000, 999999),
                'model_year' => rand(2018, 2024),
                'color' => ['Pearl White', 'Metallic Silver', 'Magma Grey', 'Silky Red', 'Nexa Blue'][$i % 5],
                'fuel_type' => $model->name === 'Nexon EV' ? 'Electric' : ($i % 3 === 0 ? 'Diesel' : 'Petrol'),
                'transmission' => $i % 4 === 0 ? 'Automatic' : 'Manual',
                'current_odometer' => rand(12000, 78000),
                'last_service_date' => now()->subMonths(rand(1, 8))->toDateString(),
                'insurance_company' => ['ICICI Lombard', 'HDFC ERGO', 'Bajaj Allianz', 'Tata AIG'][$i % 4],
                'insurance_policy_number' => 'POL-IN-' . rand(100000, 999999),
                'insurance_expiry_date' => now()->addMonths(rand(2, 11))->toDateString(),
                'puc_expiry_date' => now()->addMonths(rand(1, 6))->toDateString(),
                'status' => 'active',
            ]);
        }

        // 10. Service Categories & 20 Services
        $secCats = [
            'General Maintenance' => ['Periodic Scheduled Maintenance', 'Engine Oil & Filter Service', 'Multi-point Safety Inspection', 'Wiper Blade Replacement'],
            'Brakes & Suspension' => ['Front Disc Brake Pad Replacement', 'Rear Brake Shoe Service & Drum Turning', 'Shock Absorber & Strut Overhaul', 'Wheel Hub Bearing Replacement'],
            'Tires & Wheels' => ['3D Computerized Wheel Alignment', 'Wheel Balancing & Weight Balancing', 'Tire Rotation & Pressure Check', 'Alloy Wheel Rim Straightening'],
            'Air Conditioning & Heating' => ['AC Gas Top-up & Leakage Test (R134a)', 'Cabin Air Filter Replacement & Disinfection', 'AC Compressor Overhaul & Clutch Coil'],
            'Electrical & Diagnostics' => ['OBD-II ECU Computer Diagnostics Scan', 'Alternator & Starter Motor Service', 'Battery Replacement & Terminal Sanitization', 'High-Voltage EV Battery Pack Diagnostics'],
            'Washing & Detailing' => ['Full Foam Wash with Underbody Cleaning', 'Interior Deep Shampoo & Leather Treatment', 'Exterior Teflon / Ceramic Gloss Coating'],
        ];

        $servicesList = [];
        $serviceCode = 100;
        foreach ($secCats as $catName => $sNames) {
            $cat = ServiceCategory::create([
                'name' => $catName,
                'slug' => \Illuminate\Support\Str::slug($catName),
            ]);

            foreach ($sNames as $sn) {
                $servicesList[] = Service::create([
                    'category_id' => $cat->id,
                    'name' => $sn,
                    'code' => 'SRV-' . $serviceCode++,
                    'standard_time_hours' => rand(1, 4) * 0.5 + 0.5,
                    'standard_labour_cost' => rand(4, 30) * 100, // ₹400 to ₹3000
                    'default_gst_rate' => 18.00,
                    'sac_code' => '998729',
                    'is_active' => true,
                ]);
            }
        }

        // 11. 50 Spare Parts across Categories & 10 Suppliers
        $suppliersData = [
            ['name' => 'Bosch Auto Parts India Ltd', 'phone' => '+91 99000 11111', 'gstin' => '27AAACB1111A1Z1', 'city' => 'Pune'],
            ['name' => 'Castrol India Lubricants', 'phone' => '+91 99000 22222', 'gstin' => '27AAACC2222B1Z2', 'city' => 'Mumbai'],
            ['name' => 'Exide Industries Ltd', 'phone' => '+91 99000 33333', 'gstin' => '27AAACE3333C1Z3', 'city' => 'Mumbai'],
            ['name' => 'Uno Minda Technologies', 'phone' => '+91 99000 44444', 'gstin' => '27AAACU4444D1Z4', 'city' => 'Pune'],
            ['name' => 'MRF Tyres & Rubber Ltd', 'phone' => '+91 99000 55555', 'gstin' => '27AAACM5555E1Z5', 'city' => 'Thane'],
            ['name' => 'Mobil 1 Engine Oils (ExxonMobil)', 'phone' => '+91 99000 66666', 'gstin' => '27AAACM6666F1Z6', 'city' => 'Mumbai'],
            ['name' => 'Brembo Brake Systems India', 'phone' => '+91 99000 77777', 'gstin' => '27AAACB7777G1Z7', 'city' => 'Pune'],
            ['name' => 'Denso Auto Components', 'phone' => '+91 99000 88888', 'gstin' => '27AAACD8888H1Z8', 'city' => 'Mumbai'],
            ['name' => 'NGK Spark Plugs India', 'phone' => '+91 99000 99999', 'gstin' => '27AAACN9999I1Z9', 'city' => 'Navi Mumbai'],
            ['name' => 'Lumax Auto Technologies', 'phone' => '+91 99001 00001', 'gstin' => '27AAACL0000J1Z0', 'city' => 'Pune'],
        ];

        $suppliers = [];
        foreach ($suppliersData as $s) {
            $suppliers[] = Supplier::create([
                'company_id' => $company->id,
                'branch_id' => $mainBranch->id,
                'name' => $s['name'],
                'phone' => $s['phone'],
                'gstin' => $s['gstin'],
                'city' => $s['city'],
                'state' => 'Maharashtra',
                'outstanding_balance' => 0.00,
            ]);
        }

        $partCats = [
            'Lubricants & Fluids' => ['Synthetic Engine Oil 5W-30 (1L)', 'Fully Synthetic 0W-20 (1L)', 'DOT 4 Brake Fluid (500ml)', 'Pre-mixed Engine Coolant Red (1L)', 'Transmission Fluid ATF (1L)', 'Windshield Washer Fluid (1L)', 'EV Battery Coolant Dielectric (5L)'],
            'Filters' => ['Spin-On Engine Oil Filter', 'Engine Air Filter Element', 'Activated Charcoal Cabin AC Filter', 'Diesel Fuel Filter Cartridge', 'Transmission Filter Kit'],
            'Brake System' => ['Ceramic Front Brake Pads (Set)', 'Semi-Metallic Brake Pads (Set)', 'Rear Brake Shoes Set', 'Front Brake Disc Rotor', 'Rear Brake Drum', 'Brake Caliper Pin Kit'],
            'Ignition & Electrical' => ['Iridium Tough Spark Plug', 'Standard Nickel Spark Plug', 'Ignition Coil Pack', 'Automotive 12V 45Ah DIN Battery', 'Automotive 12V 65Ah Battery', 'Heavy Duty Starter Relay', 'Headlamp Bulb H4 12V 55/60W', 'LED Projector Fog Lamp Kit'],
            'Suspension & Steering' => ['Lower Suspension Arm Assembly', 'Front Shock Absorber Strut', 'Rear Gas Shock Absorber', 'Tie Rod End Left/Right', 'Steering Rack Boot Kit', 'Stabilizer Link Rod'],
            'Belts & Hoses' => ['Engine Serpentine Drive Belt 6PK1180', 'Timing Belt Kit with Tensioner', 'Radiator Upper EPDM Hose', 'Radiator Lower EPDM Hose', 'AC Compressor Drive Belt'],
            'Tires & Consumables' => ['Passenger Car Radial Tire 185/65 R15', 'SUV All-Terrain Tire 215/60 R16', 'Wheel Weight Clip-on 20g', 'Wheel Weight Adhesive 5g (Strip)', 'Tire Tubeless Valve Stem', 'Mechanic Penetrating Rust Spray 400ml', 'Brake Cleaner Aerosol 500ml'],
            'Body & Accessories' => ['Frameless Silicone Wiper Blade 24 inch', 'Frameless Silicone Wiper Blade 16 inch', 'Rear Windshield Wiper 12 inch', 'Rear View Mirror Glass Assembly', 'Bumper Retainer Fastener Clips (Pack of 10)'],
        ];

        $spareParts = [];
        $partNumberIndex = 1001;
        foreach ($partCats as $pCatName => $partsList) {
            $cat = SparePartCategory::create([
                'name' => $pCatName,
                'code' => \Illuminate\Support\Str::slug($pCatName),
            ]);

            foreach ($partsList as $pName) {
                $cost = rand(15, 350) * 10;
                $mrp = round($cost * 1.35, -1);
                $sell = round($cost * 1.25, -1);
                $stock = rand(8, 45);

                $part = SparePart::create([
                    'company_id' => $company->id,
                    'branch_id' => $mainBranch->id,
                    'category_id' => $cat->id,
                    'part_number' => 'PART-IN-' . $partNumberIndex++,
                    'sku' => 'SKU-' . rand(10000, 99999),
                    'name' => $pName,
                    'brand' => ['Bosch', 'Castrol', 'Exide', 'Uno Minda', 'Mobil', 'Brembo', 'Denso', 'NGK', 'Lumax'][rand(0, 8)],
                    'unit' => str_contains($pName, '(1L)') || str_contains($pName, 'Fluid') ? 'Litre' : (str_contains($pName, 'Set') ? 'Set' : 'Pcs'),
                    'purchase_price' => $cost,
                    'selling_price' => $sell,
                    'mrp' => $mrp,
                    'min_stock_alert' => 5,
                    'current_stock' => $stock,
                    'bin_location' => 'RACK-' . chr(rand(65, 70)) . '-' . rand(1, 9),
                    'hsn_sac_code' => '8708',
                    'gst_rate' => 18.00,
                    'is_active' => true,
                ]);

                // Initial ledger entry
                StockLedger::create([
                    'spare_part_id' => $part->id,
                    'branch_id' => $mainBranch->id,
                    'transaction_type' => 'purchase_in',
                    'reference_type' => 'InitialStock',
                    'quantity' => $stock,
                    'unit_cost' => $cost,
                    'balance_after' => $stock,
                    'remarks' => 'Opening stock valuation',
                    'created_by' => $users['inventory@automobile.com']->id,
                ]);

                $spareParts[] = $part;
            }
        }

        // 12. 5 Service Packages
        $packages = [
            ['name' => 'Silver Periodic Service Package', 'code' => 'PKG-SILVER', 'price' => 2499, 'desc' => 'Comprehensive 40-point inspection, engine oil replacement, oil filter, and general checkup.'],
            ['name' => 'Gold Comprehensive Service Package', 'code' => 'PKG-GOLD', 'price' => 4999, 'desc' => 'Includes full synthetic oil, air & oil filters, wheel alignment & balancing, and AC inspection.'],
            ['name' => 'Monsoon Shield Health Package', 'code' => 'PKG-MONSOON', 'price' => 1999, 'desc' => 'Brake servicing, wiper blade check, underbody anti-rust inspection, battery health report.'],
            ['name' => 'Express Wash & Lube Hub', 'code' => 'PKG-EXPRESS', 'price' => 1299, 'desc' => 'Top fluid top-ups, tire pressure calibration, full exterior foam wash and interior vacuuming.'],
            ['name' => 'EV High-Voltage Health Check', 'code' => 'PKG-EV-PLUS', 'price' => 3499, 'desc' => 'Battery state-of-health (SoH) diagnostics, thermal management coolant check, regen brake test.'],
        ];

        foreach ($packages as $pkg) {
            $package = ServicePackage::create([
                'name' => $pkg['name'],
                'code' => $pkg['code'],
                'description' => $pkg['desc'],
                'package_price' => $pkg['price'],
                'discount_percent' => 10.00,
                'valid_days' => 365,
                'is_active' => true,
            ]);

            // Add sample items
            ServicePackageItem::create([
                'service_package_id' => $package->id,
                'service_id' => $servicesList[0]->id,
                'item_type' => 'service',
                'quantity' => 1,
            ]);
            ServicePackageItem::create([
                'service_package_id' => $package->id,
                'spare_part_id' => $spareParts[0]->id,
                'item_type' => 'part',
                'quantity' => 1,
            ]);
        }

        // 13. Insurance Companies
        $insuranceCompanies = [
            ['name' => 'ICICI Lombard General Insurance', 'code' => 'ICICI-LOMBARD', 'phone' => '1800 2666'],
            ['name' => 'HDFC ERGO General Insurance', 'code' => 'HDFC-ERGO', 'phone' => '1800 266 4332'],
            ['name' => 'Bajaj Allianz General Insurance', 'code' => 'BAJAJ-ALLIANZ', 'phone' => '1800 209 5858'],
            ['name' => 'Tata AIG General Insurance', 'code' => 'TATA-AIG', 'phone' => '1800 266 7780'],
            ['name' => 'The New India Assurance Co. Ltd', 'code' => 'NEW-INDIA', 'phone' => '1800 209 1415'],
        ];
        $insCorps = [];
        foreach ($insuranceCompanies as $ic) {
            $insCorps[] = InsuranceCompany::create($ic);
        }

        // 14. 10 Appointments
        $appTypes = ['Periodic Maintenance', 'Brake Overhaul', 'Wheel Alignment', 'AC Repair', 'Engine Tune-up'];
        $appStatuses = ['scheduled', 'confirmed', 'arrived', 'in_progress', 'completed'];
        for ($i = 0; $i < 10; $i++) {
            Appointment::create([
                'appointment_number' => 'APT-2026-' . sprintf('%04d', $i + 1),
                'company_id' => $company->id,
                'branch_id' => $mainBranch->id,
                'customer_id' => $customers[$i]->id,
                'vehicle_id' => $vehicles[$i]->id,
                'service_bay_id' => $serviceBays[$i % count($serviceBays)]->id,
                'appointment_date' => now()->addDays($i - 4)->toDateString(),
                'appointment_time' => sprintf('%02d:00:00', rand(9, 16)),
                'service_type' => $appTypes[$i % count($appTypes)],
                'status' => $appStatuses[$i % count($appStatuses)],
                'estimated_cost' => rand(25, 80) * 100,
                'customer_notes' => 'Customer requested general maintenance and vibration check.',
            ]);
        }

        // 15. 15 Job Cards across realistic states
        $statuses = [
            'draft',
            'pending_estimate',
            'estimate_approved',
            'work_in_progress',
            'work_in_progress',
            'quality_check',
            'ready_for_delivery',
            'delivered',
            'delivered',
            'delivered',
            'work_in_progress',
            'estimate_approved',
            'delivered',
            'ready_for_delivery',
            'delivered'
        ];

        $advisors = [$users['advisor@automobile.com'], $users['advisor2@automobile.com']];
        $mechanics = [
            $users['mechanic@automobile.com'],
            $users['tech1@automobile.com'],
            $users['tech2@automobile.com'],
            $users['tech3@automobile.com']
        ];

        $jobCards = [];
        for ($i = 0; $i < 15; $i++) {
            $status = $statuses[$i];
            $veh = $vehicles[$i];
            $cust = $veh->customer;
            $bay = $serviceBays[$i % count($serviceBays)];
            $advisor = $advisors[$i % count($advisors)];
            $mechanic = $mechanics[$i % count($mechanics)];

            $jc = JobCard::create([
                'job_card_number' => sprintf("JC-2026-%04d", $i + 1),
                'company_id' => $company->id,
                'branch_id' => $mainBranch->id,
                'customer_id' => $cust->id,
                'vehicle_id' => $veh->id,
                'service_advisor_id' => $advisor->id,
                'lead_mechanic_id' => $mechanic->id,
                'service_bay_id' => in_array($status, ['work_in_progress', 'quality_check']) ? $bay->id : null,
                'job_type' => ['periodic_maintenance', 'general_repair', 'accidental', 'electrical'][$i % 4],
                'priority' => ['low', 'medium', 'high', 'urgent'][$i % 4],
                'status' => $status,
                'check_in_time' => now()->subDays(15 - $i)->setTime(9 + ($i % 4), 30),
                'promised_delivery_time' => now()->subDays(14 - $i)->setTime(18, 00),
                'actual_delivery_time' => $status === 'delivered' ? now()->subDays(14 - $i)->setTime(17, 30) : null,
                'delivered_at' => $status === 'delivered' ? now()->subDays(14 - $i)->setTime(17, 30) : null,
                'odometer_in' => $veh->current_odometer,
                'fuel_level' => ['25%', '50%', '75%', 'Full'][$i % 4],
                'customer_complaints' => '1. Periodic 40,000 km general service.' . "\n" . '2. Squeaking noise from front brake pads during deceleration.' . "\n" . '3. AC cooling feels sluggish in city traffic.',
                'diagnosis_remarks' => 'Brake pads worn down to 3mm. Cabin pollen filter clogged. Engine oil viscosity degraded.',
                'estimated_labour' => 1800.00,
                'estimated_parts' => 3200.00,
                'estimated_total' => 5000.00,
                'final_labour' => 1800.00,
                'final_parts' => 3200.00,
                'final_discount' => 200.00,
                'final_tax' => 864.00,
                'final_grand_total' => 5664.00,
                'gate_pass_number' => in_array($status, ['ready_for_delivery', 'delivered']) ? sprintf("GP-2026-%04d", $i + 1) : null,
                'gate_pass_generated_at' => in_array($status, ['ready_for_delivery', 'delivered']) ? now()->subDays(14 - $i) : null,
            ]);

            // Add services
            $selectedService = $servicesList[$i % count($servicesList)];
            JobCardService::create([
                'job_card_id' => $jc->id,
                'service_id' => $selectedService->id,
                'service_name' => $selectedService->name,
                'mechanic_id' => $mechanic->id,
                'estimated_hours' => 1.5,
                'actual_hours' => in_array($status, ['quality_check', 'ready_for_delivery', 'delivered']) ? 1.5 : 0.0,
                'labour_charge' => $selectedService->standard_labour_cost,
                'gst_rate' => 18.00,
                'tax_amount' => round($selectedService->standard_labour_cost * 0.18, 2),
                'total' => round($selectedService->standard_labour_cost * 1.18, 2),
                'status' => in_array($status, ['quality_check', 'ready_for_delivery', 'delivered']) ? 'completed' : 'in_progress',
            ]);

            // Add parts
            $selectedPart = $spareParts[$i % count($spareParts)];
            JobCardPart::create([
                'job_card_id' => $jc->id,
                'spare_part_id' => $selectedPart->id,
                'part_name' => $selectedPart->name,
                'part_number' => $selectedPart->part_number,
                'quantity' => 1,
                'unit_price' => $selectedPart->selling_price,
                'discount' => 0.00,
                'gst_rate' => 18.00,
                'tax_amount' => round($selectedPart->selling_price * 0.18, 2),
                'total_price' => round($selectedPart->selling_price * 1.18, 2),
                'status' => in_array($status, ['work_in_progress', 'quality_check', 'ready_for_delivery', 'delivered']) ? 'issued' : 'allocated',
                'issued_at' => now()->subDays(15 - $i),
            ]);

            // Digital Vehicle Inspection for each Job Card
            VehicleInspection::create([
                'inspection_number' => sprintf("INS-2026-%04d", $i + 1),
                'branch_id' => $mainBranch->id,
                'vehicle_id' => $veh->id,
                'customer_id' => $cust->id,
                'inspector_id' => $advisor->id,
                'inspection_date' => now()->subDays(15 - $i),
                'odometer_reading' => $veh->current_odometer,
                'fuel_level' => '50_percent',
                'scratches_dents_data' => [
                    ['part' => 'Rear Bumper', 'type' => 'Minor Scratch', 'severity' => 'low'],
                    ['part' => 'Left Front Fender', 'type' => 'Stone Chip', 'severity' => 'low'],
                ],
                'exterior_checks' => ['wipers' => 'ok', 'headlights' => 'ok', 'taillights' => 'ok', 'mirrors' => 'ok'],
                'interior_checks' => ['dashboard_warning' => 'none', 'ac_blower' => 'ok', 'horn' => 'ok'],
                'mechanical_checks' => ['engine_oil_level' => 'low', 'coolant_level' => 'ok', 'brake_fluid' => 'ok'],
                'tire_conditions' => ['front_left' => '4mm', 'front_right' => '4mm', 'rear_left' => '5mm', 'rear_right' => '5mm'],
                'tool_kit_present' => true,
                'spare_wheel_present' => true,
                'status' => 'completed',
            ]);

            // 10 Repair Estimates (for first 10 job cards)
            if ($i < 10) {
                $est = RepairEstimate::create([
                    'estimate_number' => sprintf("EST-2026-%04d", $i + 1),
                    'job_card_id' => $jc->id,
                    'customer_id' => $cust->id,
                    'vehicle_id' => $veh->id,
                    'branch_id' => $mainBranch->id,
                    'subtotal_labour' => 1500.00,
                    'subtotal_parts' => 2500.00,
                    'discount_amount' => 100.00,
                    'tax_amount' => 702.00,
                    'grand_total' => 4602.00,
                    'status' => in_array($status, ['estimate_approved', 'work_in_progress', 'quality_check', 'ready_for_delivery', 'delivered']) ? 'approved' : 'sent',
                    'customer_notes' => 'Customer authorized estimate via phone call.',
                    'approved_at' => now()->subDays(14 - $i),
                    'created_by' => $advisor->id,
                ]);

                RepairEstimateItem::create([
                    'repair_estimate_id' => $est->id,
                    'item_type' => 'service',
                    'item_id' => $selectedService->id,
                    'description' => $selectedService->name,
                    'quantity' => 1,
                    'unit_price' => 1500.00,
                    'tax_percent' => 18.00,
                    'tax_amount' => 270.00,
                    'total_amount' => 1770.00,
                ]);

                RepairEstimateItem::create([
                    'repair_estimate_id' => $est->id,
                    'item_type' => 'part',
                    'item_id' => $selectedPart->id,
                    'description' => $selectedPart->name,
                    'quantity' => 1,
                    'unit_price' => 2500.00,
                    'tax_percent' => 18.00,
                    'tax_amount' => 450.00,
                    'total_amount' => 2950.00,
                ]);
            }

            // Quality Inspection for tested job cards
            if (in_array($status, ['quality_check', 'ready_for_delivery', 'delivered'])) {
                QualityInspection::create([
                    'job_card_id' => $jc->id,
                    'inspector_id' => $users['manager@automobile.com']->id,
                    'road_test_done' => true,
                    'engine_sound_ok' => true,
                    'brake_test_ok' => true,
                    'ac_cooling_ok' => true,
                    'electrical_ok' => true,
                    'wheel_nut_torqued' => true,
                    'cleaning_washing_ok' => true,
                    'passed' => true,
                    'rework_required' => false,
                    'inspected_at' => now()->subDays(14 - $i),
                ]);
            }

            $jobCards[] = $jc;
        }

        // 16. 10 Invoices & 10 Payments
        for ($i = 0; $i < 10; $i++) {
            $jc = $jobCards[$i];
            $cust = $jc->customer;
            $veh = $jc->vehicle;

            $invLabour = 1800.00;
            $invParts = 2800.00;
            $taxable = $invLabour + $invParts;
            $cgst = round($taxable * 0.09, 2);
            $sgst = round($taxable * 0.09, 2);
            $totalTax = $cgst + $sgst;
            $grand = $taxable + $totalTax;

            $isPaid = in_array($jc->status, ['ready_for_delivery', 'delivered']);

            $invoice = Invoice::create([
                'invoice_number' => sprintf("INV-2026-%04d", $i + 1),
                'company_id' => $company->id,
                'branch_id' => $mainBranch->id,
                'customer_id' => $cust->id,
                'vehicle_id' => $veh->id,
                'job_card_id' => $jc->id,
                'invoice_date' => now()->subDays(10 - $i)->toDateString(),
                'due_date' => now()->subDays(3 - $i)->toDateString(),
                'invoice_type' => 'tax_invoice',
                'subtotal_labour' => $invLabour,
                'subtotal_parts' => $invParts,
                'discount_amount' => 0.00,
                'taxable_amount' => $taxable,
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => 0.00,
                'total_tax' => $totalTax,
                'grand_total' => $grand,
                'paid_amount' => $isPaid ? $grand : 0.00,
                'balance_due' => $isPaid ? 0.00 : $grand,
                'payment_status' => $isPaid ? 'paid' : 'unpaid',
                'notes' => 'Thank you for choosing ABC Automobile Care!',
                'terms_and_conditions' => '1. All parts carry standard manufacturer warranty.' . "\n" . '2. Labour warranted for 30 days or 1,000 km.',
                'created_by' => $users['accountant@automobile.com']->id,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'labour',
                'name' => 'Periodic Service Labour & Safety Check',
                'hsn_sac' => '998729',
                'quantity' => 1,
                'unit_price' => $invLabour,
                'taxable_value' => $invLabour,
                'gst_rate' => 18.00,
                'cgst_amount' => round($invLabour * 0.09, 2),
                'sgst_amount' => round($invLabour * 0.09, 2),
                'total_amount' => round($invLabour * 1.18, 2),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'part',
                'name' => 'Synthetic Engine Oil & Filter Kit',
                'hsn_sac' => '8708',
                'quantity' => 1,
                'unit_price' => $invParts,
                'taxable_value' => $invParts,
                'gst_rate' => 18.00,
                'cgst_amount' => round($invParts * 0.09, 2),
                'sgst_amount' => round($invParts * 0.09, 2),
                'total_amount' => round($invParts * 1.18, 2),
            ]);

            if ($isPaid) {
                Payment::create([
                    'payment_number' => sprintf("PAY-2026-%04d", $i + 1),
                    'invoice_id' => $invoice->id,
                    'customer_id' => $cust->id,
                    'branch_id' => $mainBranch->id,
                    'payment_date' => now()->subDays(10 - $i)->toDateString(),
                    'amount' => $grand,
                    'payment_method' => ['upi', 'card', 'cash'][$i % 3],
                    'transaction_reference' => 'TXN-UPI-' . rand(10000000, 99999999),
                    'notes' => 'Full payment received at delivery counter.',
                    'received_by' => $users['accountant@automobile.com']->id,
                ]);
            }
        }

        // 17. 5 Insurance Claims
        $claimStatuses = ['initiated', 'survey_scheduled', 'approved', 'in_repair', 'settled'];
        for ($i = 0; $i < 5; $i++) {
            InsuranceClaim::create([
                'claim_number' => sprintf("CLM-2026-%04d", $i + 1),
                'job_card_id' => $jobCards[$i]->id,
                'vehicle_id' => $vehicles[$i]->id,
                'customer_id' => $vehicles[$i]->customer_id,
                'insurance_company_id' => $insCorps[$i % count($insCorps)]->id,
                'policy_number' => 'POL-CLM-' . rand(100000, 999999),
                'claim_date' => now()->subDays(20 - $i * 3)->toDateString(),
                'surveyor_name' => 'K. V. Ramanathan',
                'surveyor_phone' => '+91 98333 4444' . $i,
                'surveyor_inspection_date' => now()->subDays(18 - $i * 3)->toDateString(),
                'estimated_claim_amount' => 24500.00,
                'approved_claim_amount' => 21000.00,
                'customer_liability' => 3500.00,
                'insurance_liability' => 21000.00,
                'status' => $claimStatuses[$i],
                'remarks' => 'Front bumper, grill and left headlamp assembly accidental claim.',
            ]);
        }

        // 18. 10 Service Reminders
        $reminderTypes = ['periodic_service', 'insurance_renewal', 'puc_renewal', 'general_checkup'];
        for ($i = 0; $i < 10; $i++) {
            ServiceReminder::create([
                'vehicle_id' => $vehicles[$i]->id,
                'customer_id' => $vehicles[$i]->customer_id,
                'reminder_type' => $reminderTypes[$i % count($reminderTypes)],
                'due_date' => now()->addDays(rand(3, 45))->toDateString(),
                'status' => $i < 4 ? 'sent' : 'pending',
                'channel' => 'whatsapp',
                'last_sent_at' => $i < 4 ? now()->subDays(2) : null,
                'notes' => 'Automated service reminder based on mileage and time threshold.',
            ]);
        }

        // 19. Customer Feedback
        for ($i = 0; $i < 5; $i++) {
            CustomerFeedback::create([
                'customer_id' => $customers[$i]->id,
                'job_card_id' => $jobCards[$i]->id,
                'rating' => 5,
                'service_quality' => 5,
                'staff_behavior' => 5,
                'timeliness' => 4,
                'value_for_money' => 5,
                'comments' => 'Excellent service turnaround. Very professional service advisors and honest billing!',
                'is_resolved' => true,
            ]);
        }

        // 20. Expenses
        $expenseCats = [
            'Workshop Tools & Equipment Maintenance',
            'Electricity & Utility Bills',
            'Staff Welfare & Refreshments',
            'Stationery & Printing Consumables',
            'Facility Lease & Rent'
        ];
        $expCatModels = [];
        foreach ($expenseCats as $ec) {
            $expCatModels[] = ExpenseCategory::create(['name' => $ec]);
        }

        for ($i = 0; $i < 8; $i++) {
            Expense::create([
                'expense_number' => sprintf("EXP-2026-%04d", $i + 1),
                'branch_id' => $mainBranch->id,
                'expense_category_id' => $expCatModels[$i % count($expCatModels)]->id,
                'amount' => rand(8, 75) * 100,
                'expense_date' => now()->subDays($i * 3)->toDateString(),
                'payment_method' => 'bank_transfer',
                'payee' => 'Vendor / Utility Authority',
                'description' => 'Regular operational expense for workshop maintenance.',
                'approved_by' => $users['manager@automobile.com']->id,
            ]);
        }

        // 21. System Settings
        SystemSetting::set('company_name', 'ABC Automobile Care & Service Centre', $mainBranch->id);
        SystemSetting::set('default_tax_rate', '18', $mainBranch->id);
        SystemSetting::set('currency_symbol', '₹', $mainBranch->id);
        SystemSetting::set('invoice_footer_note', 'Thank you for choosing ABC Automobile Care! Drive Safe.', $mainBranch->id);
    }
}
