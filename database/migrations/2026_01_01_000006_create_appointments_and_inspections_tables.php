<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number', 50)->unique();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('service_bay_id')->nullable()->constrained('service_bays')->nullOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('service_type', 100)->default('Periodic Maintenance');
            $table->enum('status', ['scheduled', 'confirmed', 'arrived', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->decimal('estimated_cost', 10, 2)->default(0.00);
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('vehicle_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_number', 50)->unique();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('inspection_date');
            $table->unsignedInteger('odometer_reading');
            $table->enum('fuel_level', ['empty', 'reserve', '25_percent', '50_percent', '75_percent', 'full'])->default('50_percent');
            $table->json('scratches_dents_data')->nullable(); // coordinates and notes for vehicle diagram
            $table->json('exterior_checks')->nullable(); // wipers, mirrors, lights, glass
            $table->json('interior_checks')->nullable(); // ac, horn, dashboard lights, seats, mats
            $table->json('mechanical_checks')->nullable(); // engine sound, fluid levels, brakes, battery
            $table->json('tire_conditions')->nullable(); // front left, front right, rear left, rear right, spare
            $table->boolean('tool_kit_present')->default(true);
            $table->boolean('spare_wheel_present')->default(true);
            $table->boolean('jack_handle_present')->default(true);
            $table->text('customer_items_inside')->nullable();
            $table->text('customer_signature')->nullable();
            $table->enum('status', ['draft', 'completed'])->default('completed');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_inspections');
        Schema::dropIfExists('appointments');
    }
};
