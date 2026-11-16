<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Gate Pass - {{ $jobCard->gate_pass_number ?? 'GP' }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 13px; color: #0f172a; padding: 32px; max-width: 650px; margin: 0 auto; border: 2px dashed #64748b; border-radius: 8px; margin-top: 20px; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: 900; text-transform: uppercase; }
        .pass-num { font-size: 16px; font-family: monospace; font-weight: bold; color: #2563eb; margin-top: 4px; }
        .row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e2e8f0; }
        .label { color: #64748b; font-weight: 600; font-size: 11px; text-transform: uppercase; }
        .val { font-weight: bold; }
        .footer { margin-top: 36px; display: flex; justify-content: space-between; }
        .sig { border-top: 1px solid #0f172a; width: 40%; text-align: center; padding-top: 4px; font-size: 11px; font-weight: bold; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0f172a; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Print Gate Pass</button>
    </div>

    <div class="header">
        <div style="font-size: 12px; font-weight: bold; color: #64748b; text-transform: uppercase;">ABC Automobile Care & Service Centre</div>
        <div class="title">VEHICLE DELIVERY GATE PASS</div>
        <div class="pass-num">{{ $jobCard->gate_pass_number }}</div>
        <div style="font-size: 11px; color: #64748b;">Issued on: {{ $jobCard->gate_pass_generated_at ? $jobCard->gate_pass_generated_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}</div>
    </div>

    <div class="row">
        <span class="label">Vehicle Registration #</span>
        <span class="val" style="font-family: monospace; font-size: 16px;">{{ $jobCard->vehicle->registration_number ?? 'N/A' }}</span>
    </div>
    <div class="row">
        <span class="label">Make & Model</span>
        <span class="val">{{ $jobCard->vehicle->brand->name ?? '' }} {{ $jobCard->vehicle->model->name ?? '' }}</span>
    </div>
    <div class="row">
        <span class="label">Customer / Recipient</span>
        <span class="val">{{ $jobCard->customer->full_name ?? 'N/A' }} ({{ $jobCard->customer->phone ?? '' }})</span>
    </div>
    <div class="row">
        <span class="label">Associated Job Card</span>
        <span class="val" style="font-family: monospace;">{{ $jobCard->job_card_number }}</span>
    </div>
    <div class="row">
        <span class="label">Billing Status</span>
        <span class="val" style="color: #16a34a;">
            @if($jobCard->invoice && $jobCard->invoice->payment_status === 'paid')
                PAID IN FULL (Invoice #{{ $jobCard->invoice->invoice_number }})
            @else
                BILLING CLEARED / AUTHORIZED
            @endif
        </span>
    </div>

    <div style="margin-top: 16px; font-size: 11px; color: #64748b; line-height: 1.4;">
        Security Notice: Security personnel are instructed to verify the vehicle plate number and physical match before allowing exit through workshop gate.
    </div>

    <div class="footer">
        <div class="sig">
            Customer / Receiver Signature
        </div>
        <div class="sig">
            Authorized Workshop Officer
        </div>
    </div>
</body>
</html>
