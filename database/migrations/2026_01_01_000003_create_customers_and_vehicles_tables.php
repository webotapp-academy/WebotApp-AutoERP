<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->enum('customer_type', ['individual', 'corporate', 'fleet'])->default('individual');
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('company_name', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 25);
            $table->string('alt_phone', 25)->nullable();
            $table->string('gstin', 20)->nullable();
            $table->string('pan', 15)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 20)->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0.00);
            $table->decimal('outstanding_balance', 12, 2)->default(0.00);
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // Two-Wheeler, Four-Wheeler Car, SUV, Commercial Vehicle, Electric Vehicle
            $table->string('code', 30)->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('vehicle_brands')->onDelete('cascade');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->onDelete('cascade');
            $table->string('name', 100);
            $table->json('fuel_types')->nullable(); // ['Petrol', 'Diesel', 'CNG', 'Electric', 'Hybrid']
            $table->decimal('engine_capacity_cc', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('vehicle_brands')->onDelete('cascade');
            $table->foreignId('model_id')->constrained('vehicle_models')->onDelete('cascade');
            $table->string('registration_number', 30)->unique();
            $table->string('vin_chassis_number', 50)->nullable();
            $table->string('engine_number', 50)->nullable();
            $table->year('model_year')->nullable();
            $table->string('color', 50)->nullable();
            $table->string('fuel_type', 30)->default('Petrol');
            $table->string('transmission', 30)->default('Manual');
            $table->unsignedInteger('current_odometer')->default(0);
            $table->date('last_service_date')->nullable();
            $table->string('insurance_company', 100)->nullable();
            $table->string('insurance_policy_number', 100)->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->date('puc_expiry_date')->nullable();
            $table->enum('status', ['active', 'in_service', 'archived'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_odometer_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->unsignedInteger('reading');
            $table->timestamp('recorded_at');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_odometer_logs');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_models');
        Schema::dropIfExists('vehicle_brands');
        Schema::dropIfExists('vehicle_types');
        Schema::dropIfExists('customers');
    }
};
