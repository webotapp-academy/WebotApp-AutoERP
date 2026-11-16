<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_cards', function (Blueprint $table) {
            $table->id();
            $table->string('job_card_number', 50)->unique();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('service_advisor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lead_mechanic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('service_bay_id')->nullable()->constrained('service_bays')->nullOnDelete();
            $table->string('job_type', 50)->default('periodic_maintenance'); // periodic_maintenance, general_repair, accidental, electrical, body_paint, express
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', [
                'draft',
                'pending_estimate',
                'estimate_approved',
                'work_in_progress',
                'quality_check',
                'ready_for_delivery',
                'delivered',
                'cancelled'
            ])->default('draft');
            $table->dateTime('check_in_time');
            $table->dateTime('promised_delivery_time')->nullable();
            $table->dateTime('actual_delivery_time')->nullable();
            $table->unsignedInteger('odometer_in')->default(0);
            $table->string('fuel_level', 30)->default('50%');
            $table->text('customer_complaints')->nullable();
            $table->text('diagnosis_remarks')->nullable();
            $table->decimal('estimated_labour', 12, 2)->default(0.00);
            $table->decimal('estimated_parts', 12, 2)->default(0.00);
            $table->decimal('estimated_total', 12, 2)->default(0.00);
            $table->decimal('final_labour', 12, 2)->default(0.00);
            $table->decimal('final_parts', 12, 2)->default(0.00);
            $table->decimal('final_discount', 12, 2)->default(0.00);
            $table->decimal('final_tax', 12, 2)->default(0.00);
            $table->decimal('final_grand_total', 12, 2)->default(0.00);
            $table->string('gate_pass_number', 50)->nullable();
            $table->dateTime('gate_pass_generated_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_card_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('service_name', 150);
            $table->foreignId('mechanic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('estimated_hours', 5, 2)->default(1.00);
            $table->decimal('actual_hours', 5, 2)->default(0.00);
            $table->decimal('labour_charge', 10, 2)->default(0.00);
            $table->decimal('gst_rate', 5, 2)->default(18.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total', 12, 2)->default(0.00);
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_card_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('spare_part_id')->constrained('spare_parts')->onDelete('cascade');
            $table->string('part_name', 200);
            $table->string('part_number', 100);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('gst_rate', 5, 2)->default(18.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_price', 12, 2);
            $table->enum('status', ['requested', 'allocated', 'issued', 'returned'])->default('requested');
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('returned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('job_card_time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('job_card_service_id')->nullable()->constrained('job_card_services')->nullOnDelete();
            $table->foreignId('mechanic_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('repair_estimates', function (Blueprint $table) {
            $table->id();
            $table->string('estimate_number', 50)->unique();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->decimal('subtotal_labour', 12, 2)->default(0.00);
            $table->decimal('subtotal_parts', 12, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->enum('status', ['draft', 'sent', 'approved', 'rejected'])->default('draft');
            $table->text('customer_notes')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('repair_estimate_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_estimate_id')->constrained('repair_estimates')->onDelete('cascade');
            $table->enum('item_type', ['service', 'part'])->default('service');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('description', 200);
            $table->decimal('quantity', 8, 2)->default(1.00);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('tax_percent', 5, 2)->default(18.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_estimate_items');
        Schema::dropIfExists('repair_estimates');
        Schema::dropIfExists('job_card_time_logs');
        Schema::dropIfExists('job_card_parts');
        Schema::dropIfExists('job_card_services');
        Schema::dropIfExists('job_cards');
    }
};

