<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Work Order - {{ $jobCard->job_card_number }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 12px; color: #1e293b; padding: 24px; line-height: 1.4; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #0f172a; }
        .subtitle { font-size: 11px; color: #64748b; }
        .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; background: #f8fafc; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 16px; }
        .label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 600; }
        .val { font-size: 12px; font-weight: bold; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f1f5f9; padding: 6px 8px; font-size: 10px; text-transform: uppercase; text-align: left; border: 1px solid #cbd5e1; }
        td { padding: 6px 8px; font-size: 11px; border: 1px solid #cbd5e1; }
        .complaints { background: #fffbeb; border: 1px solid #fef3c7; padding: 10px; border-radius: 6px; margin-bottom: 16px; font-size: 11px; white-space: pre-line; }
        .footer { margin-top: 32px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .sig-box { border-top: 1px solid #94a3b8; padding-top: 4px; font-size: 11px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Print Document</button>
    </div>

    <div class="header">
        <div>
            <div class="title">ABC Automobile Care & Service Centre</div>
            <div class="subtitle">Workshop Job Card & Technician Floor Work Order</div>
            <div style="font-size: 10px; color: #64748b; margin-top: 4px;">Plot 42, Sector 18, Automotive Zone, Mumbai • GSTIN: 27AABCU9603R1ZM</div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-family: monospace; font-weight: bold;">{{ $jobCard->job_card_number }}</div>
            <div class="subtitle">Date: {{ $jobCard->check_in_time->format('d M Y, h:i A') }}</div>
            <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; margin-top: 4px;">{{ $jobCard->priority }} PRIORITY</div>
        </div>
    </div>

    <div class="grid">
        <div>
            <div class="label">Vehicle Plate</div>
            <div class="val" style="font-family: monospace;">{{ $jobCard->vehicle->registration_number ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="label">Make & Model</div>
            <div class="val">{{ $jobCard->vehicle->brand->name ?? '' }} {{ $jobCard->vehicle->model->name ?? '' }}</div>
        </div>
        <div>
            <div class="label">Customer Name</div>
            <div class="val">{{ $jobCard->customer->full_name ?? 'N/A' }}</div>
        </div>
        <div>
            <div class="label">Odometer / Fuel</div>
            <div class="val">{{ number_format($jobCard->odometer_in) }} KM / {{ $jobCard->fuel_level }}</div>
        </div>
    </div>

    <div class="label" style="margin-bottom: 4px;">Customer Reported Complaints & Job Scope</div>
    <div class="complaints">{{ $jobCard->customer_complaints }}</div>

    <div class="label" style="margin-bottom: 4px;">Labour Operations Assigned</div>
    <table>
        <thead>
            <tr>
                <th>Operation / Service Name</th>
                <th>Technician</th>
                <th>Est Hours</th>
                <th>Labour (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobCard->services as $s)
                <tr>
                    <td><strong>{{ $s->service_name }}</strong></td>
                    <td>{{ $s->mechanic->name ?? 'Workshop Staff' }}</td>
                    <td>{{ $s->estimated_hours }}</td>
                    <td>₹{{ number_format($s->labour_charge, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="label" style="margin-bottom: 4px;">Parts & Materials Requisition</div>
    <table>
        <thead>
            <tr>
                <th>Part Description</th>
                <th>Part #</th>
                <th>Qty</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobCard->parts as $p)
                <tr>
                    <td><strong>{{ $p->part_name }}</strong></td>
                    <td>{{ $p->part_number }}</td>
                    <td>{{ $p->quantity }}</td>
                    <td style="text-transform: uppercase;">{{ $p->status }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align: center; color: #94a3b8;">No spare parts allocated yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="sig-box">
            Lead Technician Signature
            <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $jobCard->leadMechanic->name ?? 'Mechanic' }}</div>
        </div>
        <div class="sig-box" style="text-align: right;">
            Service Advisor Signature
            <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $jobCard->serviceAdvisor->name ?? 'Advisor' }}</div>
        </div>
    </div>
</body>
</html>
