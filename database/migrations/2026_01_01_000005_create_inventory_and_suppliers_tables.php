<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spare_part_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('name', 150);
            $table->string('company_name', 150)->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 25);
            $table->string('email', 150)->nullable();
            $table->string('gstin', 20)->nullable();
            $table->string('pan', 15)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 20)->nullable();
            $table->unsignedInteger('payment_terms_days')->default(30);
            $table->decimal('outstanding_balance', 12, 2)->default(0.00);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('category_id')->constrained('spare_part_categories')->onDelete('cascade');
            $table->string('part_number', 100)->unique();
            $table->string('sku', 100)->nullable();
            $table->string('name', 200);
            $table->string('brand', 100)->nullable(); // Bosch, Mobil, Castrol, Uno Minda, Denso, NGK, Brembo, etc.
            $table->string('barcode', 100)->nullable();
            $table->string('unit', 30)->default('Pcs'); // Pcs, Litre, Set, Meter, Kg, Pair
            $table->decimal('purchase_price', 10, 2)->default(0.00);
            $table->decimal('selling_price', 10, 2)->default(0.00);
            $table->decimal('mrp', 10, 2)->default(0.00);
            $table->integer('min_stock_alert')->default(5);
            $table->integer('current_stock')->default(0);
            $table->string('bin_location', 50)->nullable();
            $table->string('hsn_sac_code', 20)->default('8708'); // HSN for Automobile Parts in India
            $table->decimal('gst_rate', 5, 2)->default(18.00); // Usually 18% or 28% in India
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add foreign key constraint to service_package_items now that spare_parts exists
        Schema::table('service_package_items', function (Blueprint $table) {
            $table->foreign('spare_part_id')->references('id')->on('spare_parts')->nullOnDelete();
        });

        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spare_part_id')->constrained('spare_parts')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->enum('transaction_type', ['purchase_in', 'job_card_issue', 'job_card_return', 'stock_adjustment', 'scrap']);
            $table->string('reference_type', 50)->nullable(); // PurchaseOrder, JobCard, Manual
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('quantity'); // Positive for in, negative for out
            $table->decimal('unit_cost', 10, 2)->default(0.00);
            $table->integer('balance_after');
            $table->string('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 50)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->enum('status', ['draft', 'ordered', 'received', 'partially_received', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('spare_part_id')->constrained('spare_parts')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('tax_percent', 5, 2)->default(18.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->integer('received_quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('service_package_items', function (Blueprint $table) {
            $table->dropForeign(['spare_part_id']);
        });
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stock_ledgers');
        Schema::dropIfExists('spare_parts');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('spare_part_categories');
    }
};

