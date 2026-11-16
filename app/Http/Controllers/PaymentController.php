<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use App\Services\BillingService;
use Exception;

class PaymentController extends Controller
{
    protected BillingService $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function index(Request $request)
    {
        $query = Payment::with(['invoice.jobCard', 'customer', 'receiver']);

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        $payments = $query->latest('payment_date')->paginate(15)->withQueryString();
        $totalCollected = Payment::sum('amount');

        return view('payments.index', compact('payments', 'totalCollected'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,upi,card,net_banking,cheque',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        try {
            $payment = $this->billingService->recordPayment(
                $invoice,
                $request->amount,
                $request->payment_method,
                $request->transaction_reference,
                auth()->id(),
                $request->notes
            );

            return back()->with('success', "Payment of ₹" . number_format($payment->amount, 2) . " received successfully! Receipt #{$payment->payment_number}");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['invoice.customer', 'invoice.vehicle', 'receiver', 'branch']);
        return view('payments.receipt', compact('payment'));
    }
}
