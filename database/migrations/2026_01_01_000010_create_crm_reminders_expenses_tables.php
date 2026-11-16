<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('reminder_type', 50)->default('periodic_service'); // periodic_service, insurance_renewal, puc_renewal, general_checkup
            $table->date('due_date');
            $table->enum('status', ['pending', 'sent', 'customer_booked', 'dismissed'])->default('pending');
            $table->string('channel', 30)->default('sms'); // sms, whatsapp, email
            $table->text('notes')->nullable();
            $table->dateTime('last_sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('job_card_id')->nullable()->constrained('job_cards')->nullOnDelete();
            $table->tinyInteger('rating')->default(5); // 1-5 stars
            $table->tinyInteger('service_quality')->default(5);
            $table->tinyInteger('staff_behavior')->default(5);
            $table->tinyInteger('timeliness')->default(5);
            $table->tinyInteger('value_for_money')->default(5);
            $table->text('comments')->nullable();
            $table->boolean('is_resolved')->default(true);
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number', 50)->unique();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('expense_category_id')->constrained('expense_categories')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('payment_method', 50)->default('cash');
            $table->string('payee', 150);
            $table->text('description')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('receipt_path')->nullable();
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('customer_feedback');
        Schema::dropIfExists('service_reminders');
    }
};

