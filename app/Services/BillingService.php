<?php

namespace App\Services;

use App\Models\JobCard;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Exception;

class BillingService
{
    protected GstService $gstService;

    public function __construct(GstService $gstService)
    {
        $this->gstService = $gstService;
    }

    /**
     * Create Invoice from completed/approved Job Card.
     */
    public function createInvoiceFromJobCard(JobCard $jobCard, int $userId, string $invoiceType = 'tax_invoice'): Invoice
    {
        return DB::transaction(function () use ($jobCard, $userId, $invoiceType) {
            // Check if invoice already exists
            if ($jobCard->invoice) {
                return $jobCard->invoice;
            }

            $year = date('Y');
            $nextSeq = (int)Invoice::max('id') + 1;
            $invoiceNumber = sprintf("INV-%s-%04d", $year, $nextSeq);
            while (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
                $nextSeq++;
                $invoiceNumber = sprintf("INV-%s-%04d", $year, $nextSeq);
            }

            $subtotalLabour = 0.00;
            $subtotalParts = 0.00;
            $taxableTotal = 0.00;
            $cgstTotal = 0.00;
            $sgstTotal = 0.00;
            $igstTotal = 0.00;

            $itemsData = [];

            // Add labour services
            foreach ($jobCard->services as $service) {
                $taxData = $this->gstService->calculateFromNet($service->labour_charge, 1, 0.00, $service->gst_rate);
                $subtotalLabour += $service->labour_charge;
                $taxableTotal += $taxData['taxable_amount'];
                $cgstTotal += $taxData['cgst_amount'];
                $sgstTotal += $taxData['sgst_amount'];
                $igstTotal += $taxData['igst_amount'];

                $itemsData[] = [
                    'item_type' => 'labour',
                    'name' => $service->service_name,
                    'hsn_sac' => $service->service?->sac_code ?? '998729',
                    'quantity' => 1,
                    'unit_price' => $service->labour_charge,
                    'discount' => 0.00,
                    'taxable_value' => $taxData['taxable_amount'],
                    'gst_rate' => $service->gst_rate,
                    'cgst_amount' => $taxData['cgst_amount'],
                    'sgst_amount' => $taxData['sgst_amount'],
                    'igst_amount' => $taxData['igst_amount'],
                    'total_amount' => $taxData['grand_total'],
                ];
            }

            // Add issued parts
            foreach ($jobCard->parts->whereIn('status', ['issued', 'allocated']) as $part) {
                $taxData = $this->gstService->calculateFromNet($part->unit_price, $part->quantity, $part->discount, $part->gst_rate);
                $subtotalParts += ($part->unit_price * $part->quantity);
                $taxableTotal += $taxData['taxable_amount'];
                $cgstTotal += $taxData['cgst_amount'];
                $sgstTotal += $taxData['sgst_amount'];
                $igstTotal += $taxData['igst_amount'];

                $itemsData[] = [
                    'item_type' => 'part',
                    'name' => $part->part_name,
                    'hsn_sac' => $part->sparePart?->hsn_sac_code ?? '8708',
                    'quantity' => $part->quantity,
                    'unit_price' => $part->unit_price,
                    'discount' => $part->discount,
                    'taxable_value' => $taxData['taxable_amount'],
                    'gst_rate' => $part->gst_rate,
                    'cgst_amount' => $taxData['cgst_amount'],
                    'sgst_amount' => $taxData['sgst_amount'],
                    'igst_amount' => $taxData['igst_amount'],
                    'total_amount' => $taxData['grand_total'],
                ];
            }

            $totalTax = $cgstTotal + $sgstTotal + $igstTotal;
            $grandTotal = $taxableTotal + $totalTax;

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'company_id' => $jobCard->company_id,
                'branch_id' => $jobCard->branch_id,
                'customer_id' => $jobCard->customer_id,
                'vehicle_id' => $jobCard->vehicle_id,
                'job_card_id' => $jobCard->id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
                'invoice_type' => $invoiceType,
                'subtotal_labour' => $subtotalLabour,
                'subtotal_parts' => $subtotalParts,
                'discount_amount' => 0.00,
                'taxable_amount' => $taxableTotal,
                'cgst_amount' => $cgstTotal,
                'sgst_amount' => $sgstTotal,
                'igst_amount' => $igstTotal,
                'total_tax' => $totalTax,
                'grand_total' => $grandTotal,
                'paid_amount' => 0.00,
                'balance_due' => $grandTotal,
                'payment_status' => 'unpaid',
                'created_by' => $userId,
            ]);

            foreach ($itemsData as $item) {
                $item['invoice_id'] = $invoice->id;
                InvoiceItem::create($item);
            }

            // Update customer outstanding balance
            $customer = Customer::find($jobCard->customer_id);
            if ($customer) {
                $customer->outstanding_balance += $grandTotal;
                $customer->save();
            }

            return $invoice;
        });
    }

    /**
     * Record a payment towards an invoice.
     */
    public function recordPayment(Invoice $invoice, float $amount, string $paymentMethod, ?string $refNumber, int $userId, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($invoice, $amount, $paymentMethod, $refNumber, $userId, $notes) {
            if ($amount <= 0) {
                throw new Exception("Payment amount must be greater than zero.");
            }

            if ($amount > $invoice->balance_due) {
                throw new Exception("Payment amount exceeds balance due of ₹" . number_format($invoice->balance_due, 2));
            }

            $year = date('Y');
            $nextSeq = (int)Payment::max('id') + 1;
            $paymentNumber = sprintf("PAY-%s-%04d", $year, $nextSeq);
            while (Payment::where('payment_number', $paymentNumber)->exists()) {
                $nextSeq++;
                $paymentNumber = sprintf("PAY-%s-%04d", $year, $nextSeq);
            }

            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'branch_id' => $invoice->branch_id,
                'payment_date' => now()->toDateString(),
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'transaction_reference' => $refNumber,
                'notes' => $notes,
                'received_by' => $userId,
            ]);

            // Update invoice paid and balance
            $invoice->paid_amount += $amount;
            $invoice->balance_due = max(0.00, round($invoice->grand_total - $invoice->paid_amount, 2));

            if ($invoice->balance_due <= 0.01) {
                $invoice->payment_status = 'paid';
            } else {
                $invoice->payment_status = 'partially_paid';
            }
            $invoice->save();

            // Update customer outstanding balance
            $customer = $invoice->customer;
            if ($customer) {
                $customer->outstanding_balance = max(0.00, $customer->outstanding_balance - $amount);
                $customer->save();
            }

            return $payment;
        });
    }
}
