<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Estimate - {{ $estimate->estimate_number }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 12px; color: #1e293b; padding: 24px; line-height: 1.4; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #0f172a; }
        .subtitle { font-size: 11px; color: #64748b; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; background: #f8fafc; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 16px; }
        .label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 600; }
        .val { font-size: 12px; font-weight: bold; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f1f5f9; padding: 6px 8px; font-size: 10px; text-transform: uppercase; text-align: left; border: 1px solid #cbd5e1; }
        td { padding: 6px 8px; font-size: 11px; border: 1px solid #cbd5e1; }
        .total-box { margin-left: auto; width: 260px; margin-top: 12px; }
        .total-row { display: flex; justify-content: space-between; padding: 4px 0; }
        .footer { margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .sig-box { border-top: 1px solid #94a3b8; padding-top: 4px; font-size: 11px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Print Estimate</button>
    </div>

    <div class="header">
        <div>
            <div class="title">ABC Automobile Care & Service Centre</div>
            <div class="subtitle">Official Repair Estimate & Quotation</div>
            <div style="font-size: 10px; color: #64748b; margin-top: 4px;">Plot 42, Sector 18, Automotive Zone, Mumbai • GSTIN: 27AABCU9603R1ZM</div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-family: monospace; font-weight: bold;">{{ $estimate->estimate_number }}</div>
            <div class="subtitle">Date: {{ $estimate->created_at->format('d M Y') }}</div>
            <div style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin-top: 4px; color: #16a34a;">Status: {{ $estimate->status }}</div>
        </div>
    </div>

    <div class="grid">
        <div>
            <div class="label">Customer Name</div>
            <div class="val">{{ $estimate->customer->full_name ?? 'N/A' }}</div>
            <div style="font-size: 10px; color: #64748b;">Phone: {{ $estimate->customer->phone ?? '' }}</div>
        </div>
        <div>
            <div class="label">Vehicle Registration</div>
            <div class="val" style="font-family: monospace;">{{ $estimate->vehicle->registration_number ?? 'N/A' }}</div>
            <div style="font-size: 10px; color: #64748b;">{{ $estimate->vehicle->brand->name ?? '' }} {{ $estimate->vehicle->model->name ?? '' }}</div>
        </div>
        <div>
            <div class="label">Associated Job Card</div>
            <div class="val" style="font-family: monospace;">{{ $estimate->jobCard->job_card_number }}</div>
            <div style="font-size: 10px; color: #64748b;">Priority: {{ strtoupper($estimate->jobCard->priority) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Rate (₹)</th>
                <th>GST Rate</th>
                <th>Tax Amount (₹)</th>
                <th style="text-align: right;">Total Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estimate->items as $it)
                <tr>
                    <td style="text-transform: uppercase; font-size: 10px; font-weight: bold;">{{ $it->item_type }}</td>
                    <td><strong>{{ $it->description }}</strong></td>
                    <td>{{ $it->quantity }}</td>
                    <td>₹{{ number_format($it->unit_price, 2) }}</td>
                    <td>{{ $it->tax_percent }}%</td>
                    <td>₹{{ number_format($it->tax_amount, 2) }}</td>
                    <td style="text-align: right; font-weight: bold;">₹{{ number_format($it->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <div class="total-row">
            <span>Subtotal Labour:</span>
            <strong>₹{{ number_format($estimate->subtotal_labour, 2) }}</strong>
        </div>
        <div class="total-row">
            <span>Subtotal Parts:</span>
            <strong>₹{{ number_format($estimate->subtotal_parts, 2) }}</strong>
        </div>
        <div class="total-row">
            <span>Applicable GST (18%):</span>
            <strong>₹{{ number_format($estimate->tax_amount, 2) }}</strong>
        </div>
        <div class="total-row" style="font-size: 14px; border-top: 2px solid #0f172a; padding-top: 6px; margin-top: 4px;">
            <span>Estimated Total:</span>
            <strong style="color: #2563eb;">₹{{ number_format($estimate->grand_total, 2) }}</strong>
        </div>
    </div>

    <div class="footer">
        <div class="sig-box">
            Customer Approval Signature
            <div style="font-size: 10px; color: #64748b; margin-top: 2px;">I authorize the repair estimate above.</div>
        </div>
        <div class="sig-box" style="text-align: right;">
            Authorized Service Advisor Signature
            <div style="font-size: 10px; color: #64748b; margin-top: 2px;">ABC Automobile Care & Service Centre</div>
        </div>
    </div>
</body>
</html>
