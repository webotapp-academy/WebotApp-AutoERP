<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->boolean('road_test_done')->default(false);
            $table->boolean('engine_sound_ok')->default(true);
            $table->boolean('brake_test_ok')->default(true);
            $table->boolean('ac_cooling_ok')->default(true);
            $table->boolean('electrical_ok')->default(true);
            $table->boolean('wheel_nut_torqued')->default(true);
            $table->boolean('cleaning_washing_ok')->default(true);
            $table->boolean('passed')->default(true);
            $table->boolean('rework_required')->default(false);
            $table->text('rework_notes')->nullable();
            $table->dateTime('inspected_at');
            $table->timestamps();
        });

        Schema::create('insurance_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number', 50)->unique();
            $table->foreignId('job_card_id')->constrained('job_cards')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('insurance_company_id')->constrained('insurance_companies')->onDelete('cascade');
            $table->string('policy_number', 100);
            $table->date('claim_date');
            $table->string('surveyor_name', 100)->nullable();
            $table->string('surveyor_phone', 25)->nullable();
            $table->date('surveyor_inspection_date')->nullable();
            $table->decimal('estimated_claim_amount', 12, 2)->default(0.00);
            $table->decimal('approved_claim_amount', 12, 2)->default(0.00);
            $table->decimal('customer_liability', 12, 2)->default(0.00);
            $table->decimal('insurance_liability', 12, 2)->default(0.00);
            $table->enum('status', [
                'initiated',
                'survey_scheduled',
                'survey_done',
                'approval_pending',
                'approved',
                'in_repair',
                'invoice_submitted',
                'settled',
                'rejected'
            ])->default('initiated');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
        Schema::dropIfExists('insurance_companies');
        Schema::dropIfExists('quality_inspections');
    }
};

