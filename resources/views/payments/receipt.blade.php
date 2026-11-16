<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 13px; color: #0f172a; padding: 24px; max-width: 500px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; margin-top: 30px; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px; }
        .title { font-size: 16px; font-weight: 900; text-transform: uppercase; }
        .receipt-num { font-size: 14px; font-family: monospace; font-weight: bold; color: #16a34a; margin-top: 2px; }
        .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; }
        .label { color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 600; }
        .val { font-weight: bold; }
        .amt-box { background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 6px; padding: 12px; text-align: center; margin: 16px 0; }
        .amt-val { font-size: 24px; font-weight: 900; font-family: monospace; color: #15803d; }
        .sig { margin-top: 36px; text-align: right; border-top: 1px solid #cbd5e1; padding-top: 4px; font-size: 10px; color: #64748b; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 14px; background: #0f172a; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Print Receipt</button>
    </div>

    <div class="header">
        <div style="font-size: 11px; font-weight: bold; color: #64748b; text-transform: uppercase;">ABC Automobile Care & Service Centre</div>
        <div class="title">OFFICIAL PAYMENT RECEIPT</div>
        <div class="receipt-num">{{ $payment->payment_number }}</div>
        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Date: {{ $payment->payment_date->format('d M Y') }}</div>
    </div>

    <div class="amt-box">
        <div class="label" style="color: #166534;">Amount Received</div>
        <div class="amt-val">₹{{ number_format($payment->amount, 2) }}</div>
    </div>

    <div class="row">
        <span class="label">Received From</span>
        <span class="val">{{ $payment->customer->full_name ?? 'Customer' }}</span>
    </div>
    <div class="row">
        <span class="label">Invoice Reference</span>
        <span class="val" style="font-family: monospace;">{{ $payment->invoice->invoice_number ?? 'N/A' }}</span>
    </div>
    <div class="row">
        <span class="label">Payment Mode</span>
        <span class="val" style="text-transform: uppercase;">{{ $payment->payment_method }}</span>
    </div>
    @if($payment->transaction_reference)
        <div class="row">
            <span class="label">Reference / UTR</span>
            <span class="val" style="font-family: monospace;">{{ $payment->transaction_reference }}</span>
        </div>
    @endif
    <div class="row">
        <span class="label">Cashier / Staff</span>
        <span class="val">{{ $payment->receiver->name ?? 'Accounts Officer' }}</span>
    </div>

    <div class="sig">
        Authorized Stamp & Signature<br>
        <strong>ABC Automobile Care & Service Centre</strong>
    </div>
</body>
</html>

