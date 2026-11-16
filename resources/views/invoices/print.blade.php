<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tax Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 11px; color: #0f172a; padding: 24px; line-height: 1.35; margin: 0 auto; max-width: 800px; }
        .tax-invoice-container { border: 1.5px solid #0f172a; padding: 16px; border-radius: 4px; }
        .header { display: flex; justify-content: space-between; border-bottom: 1.5px solid #0f172a; padding-bottom: 10px; margin-bottom: 12px; }
        .comp-title { font-size: 16px; font-weight: 900; text-transform: uppercase; color: #0f172a; }
        .inv-badge { font-size: 14px; font-weight: 800; text-transform: uppercase; border: 1px solid #0f172a; padding: 4px 8px; text-align: center; }
        .party-grid { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #0f172a; padding-bottom: 10px; margin-bottom: 12px; gap: 16px; }
        .box-title { font-size: 9px; font-weight: 800; text-transform: uppercase; color: #475569; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #f8fafc; padding: 5px 6px; font-size: 9px; text-transform: uppercase; text-align: left; border: 1px solid #0f172a; font-weight: 800; }
        td { padding: 5px 6px; font-size: 10px; border: 1px solid #cbd5e1; }
        .num { text-align: right; font-family: monospace; }
        .total-box { margin-left: auto; width: 300px; border: 1px solid #0f172a; }
        .total-row { display: flex; justify-content: space-between; padding: 3px 8px; font-size: 10px; border-bottom: 1px solid #e2e8f0; }
        .grand-row { display: flex; justify-content: space-between; padding: 6px 8px; font-size: 12px; font-weight: 900; background: #f8fafc; border-bottom: 1px solid #0f172a; }
        .footer { margin-top: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
        .terms { font-size: 9px; color: #475569; line-height: 1.3; }
        .sig-box { border-top: 1px solid #0f172a; text-align: center; padding-top: 4px; font-size: 10px; font-weight: bold; margin-top: 40px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #0f172a; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Print Tax Invoice</button>
    </div>

    <div class="tax-invoice-container">
        <!-- Top Header -->
        <div class="header">
            <div>
                <div class="comp-title">{{ $invoice->company->name ?? 'ABC Automobile Care & Service Centre' }}</div>
                <div style="font-size: 10px; color: #475569;">{{ $invoice->company->legal_name ?? 'ABC Auto Care Solutions Pvt Ltd' }}</div>
                <div style="font-size: 10px; margin-top: 2px;">{{ $invoice->company->address ?? 'Plot 42, Sector 18, Automotive Zone' }}, {{ $invoice->company->city ?? 'Mumbai' }}, {{ $invoice->company->state ?? 'Maharashtra' }} - {{ $invoice->company->postal_code ?? '400705' }}</div>
                <div style="font-size: 10px; font-weight: bold; margin-top: 2px; font-family: monospace;">GSTIN: {{ $invoice->company->gstin ?? '27AABCU9603R1ZM' }} • PAN: {{ $invoice->company->pan_number ?? 'AABCU9603R' }}</div>
            </div>
            <div style="text-align: right;">
                <div class="inv-badge">TAX INVOICE</div>
                <div style="font-size: 13px; font-family: monospace; font-weight: bold; margin-top: 6px;">{{ $invoice->invoice_number }}</div>
                <div style="font-size: 10px; color: #475569;">Invoice Date: <strong>{{ $invoice->invoice_date->format('d/m/Y') }}</strong></div>
                @if($invoice->jobCard)
                    <div style="font-size: 10px; color: #475569;">Job Card #: <strong>{{ $invoice->jobCard->job_card_number }}</strong></div>
                @endif
            </div>
        </div>

        <!-- Party Details -->
        <div class="party-grid">
            <div>
                <div class="box-title">Details of Receiver / Billed To:</div>
                <div style="font-weight: bold; font-size: 12px;">{{ $invoice->customer->full_name ?? 'N/A' }}</div>
                @if($invoice->customer->company_name)
                    <div style="font-weight: 600;">{{ $invoice->customer->company_name }}</div>
                @endif
                <div>{{ $invoice->customer->address ?? '' }}, {{ $invoice->customer->city ?? '' }} - {{ $invoice->customer->pincode ?? '' }}</div>
                <div>Phone: <strong>{{ $invoice->customer->phone }}</strong></div>
                <div style="font-family: monospace; font-weight: bold; margin-top: 2px;">Customer GSTIN: {{ $invoice->customer->gstin ?? 'URP (Unregistered Person)' }}</div>
            </div>
            <div>
                <div class="box-title">Vehicle & Service Details:</div>
                <div style="font-size: 13px; font-family: monospace; font-weight: 900;">{{ $invoice->vehicle->registration_number ?? 'N/A' }}</div>
                <div>{{ $invoice->vehicle->brand->name ?? '' }} {{ $invoice->vehicle->model->name ?? '' }} ({{ $invoice->vehicle->fuel_type }})</div>
                <div>Place of Supply: <strong>Maharashtra (State Code: 27)</strong></div>
                <div>Payment Terms: <strong>Immediate / Cash / UPI</strong></div>
                <div>Payment Status: <strong style="text-transform: uppercase;">{{ $invoice->payment_status }}</strong></div>
            </div>
        </div>

        <!-- Line Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">#</th>
                    <th>Description of Goods / Services</th>
                    <th style="width: 55px;">HSN/SAC</th>
                    <th style="width: 35px; text-align: center;">Qty</th>
                    <th style="width: 65px;" class="num">Rate (₹)</th>
                    <th style="width: 70px;" class="num">Taxable (₹)</th>
                    <th style="width: 60px;" class="num">CGST (₹)</th>
                    <th style="width: 60px;" class="num">SGST (₹)</th>
                    <th style="width: 75px;" class="num">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $it)
                    <tr>
                        <td style="text-align: center; color: #64748b;">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $it->name }}</strong>
                            <span style="font-size: 9px; color: #64748b; text-transform: uppercase;">({{ $it->item_type }})</span>
                        </td>
                        <td style="font-family: monospace; font-size: 9px;">{{ $it->hsn_sac ?? '998729' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $it->quantity }}</td>
                        <td class="num">{{ number_format($it->unit_price, 2) }}</td>
                        <td class="num"><strong>{{ number_format($it->taxable_value, 2) }}</strong></td>
                        <td class="num">{{ number_format($it->cgst_amount, 2) }}</td>
                        <td class="num">{{ number_format($it->sgst_amount, 2) }}</td>
                        <td class="num" style="font-weight: bold;">{{ number_format($it->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals & Calculations -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="max-width: 420px; font-size: 10px;">
                <div style="border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: #f8fafc;">
                    <strong>Bank Details for Electronic Transfer / IMPS / NEFT:</strong><br>
                    Bank Name: HDFC Bank Ltd • Account: 50200012345678<br>
                    IFSC: HDFC0000123 • Branch: Sector 18 Automotive Zone, Mumbai<br>
                    UPI ID: abcautocare@hdfcbank
                </div>
            </div>

            <div class="total-box">
                <div class="total-row">
                    <span>Total Taxable Amount:</span>
                    <strong style="font-family: monospace;">₹{{ number_format($invoice->taxable_amount, 2) }}</strong>
                </div>
                <div class="total-row">
                    <span>Central GST (CGST 9%):</span>
                    <strong style="font-family: monospace;">₹{{ number_format($invoice->cgst_amount, 2) }}</strong>
                </div>
                <div class="total-row">
                    <span>State GST (SGST 9%):</span>
                    <strong style="font-family: monospace;">₹{{ number_format($invoice->sgst_amount, 2) }}</strong>
                </div>
                <div class="grand-row">
                    <span>Invoice Grand Total:</span>
                    <span style="font-family: monospace; color: #0f172a;">₹{{ number_format($invoice->grand_total, 2) }}</span>
                </div>
                <div class="total-row" style="color: #16a34a; font-weight: bold;">
                    <span>Amount Paid:</span>
                    <span style="font-family: monospace;">₹{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="total-row" style="color: #dc2626; font-weight: bold; border-bottom: none;">
                    <span>Balance Due:</span>
                    <span style="font-family: monospace;">₹{{ number_format($invoice->balance_due, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer & Signatures -->
        <div class="footer">
            <div class="terms">
                <strong>Terms & Conditions:</strong><br>
                1. Warranty on new genuine spare parts is subject to manufacturer terms.<br>
                2. Labour is guaranteed for 30 days or 1,000 KM whichever occurs first.<br>
                3. Vehicles stored beyond 48 hours after readiness notice incur parking charges.<br>
                4. Disputes subject to Mumbai jurisdiction only.
            </div>
            <div>
                <div class="sig-box">
                    For {{ $invoice->company->legal_name ?? 'ABC Auto Care Solutions Pvt Ltd' }}<br>
                    <span style="font-size: 8px; font-weight: normal; color: #64748b;">(Authorized Signatory)</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
