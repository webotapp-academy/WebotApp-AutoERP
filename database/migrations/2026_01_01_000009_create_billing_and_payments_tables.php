<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('job_card_id')->nullable()->constrained('job_cards')->nullOnDelete();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->enum('invoice_type', ['tax_invoice', 'proforma', 'cash_memo'])->default('tax_invoice');
            $table->decimal('subtotal_labour', 12, 2)->default(0.00);
            $table->decimal('subtotal_parts', 12, 2)->default(0.00);
            $table->enum('discount_type', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('discount_rate', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('taxable_amount', 12, 2)->default(0.00);
            $table->decimal('cgst_amount', 12, 2)->default(0.00); // 9% for 18% GST intra-state
            $table->decimal('sgst_amount', 12, 2)->default(0.00); // 9% for 18% GST intra-state
            $table->decimal('igst_amount', 12, 2)->default(0.00); // 18% for inter-state
            $table->decimal('total_tax', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('balance_due', 12, 2)->default(0.00);
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid', 'overdue'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->enum('item_type', ['labour', 'part'])->default('labour');
            $table->string('name', 200);
            $table->string('hsn_sac', 20)->nullable();
            $table->decimal('quantity', 8, 2)->default(1.00);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('taxable_value', 12, 2);
            $table->decimal('gst_rate', 5, 2)->default(18.00);
            $table->decimal('cgst_amount', 10, 2)->default(0.00);
            $table->decimal('sgst_amount', 10, 2)->default(0.00);
            $table->decimal('igst_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 50)->unique();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'upi', 'card', 'net_banking', 'cheque'])->default('cash');
            $table->string('transaction_reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};

