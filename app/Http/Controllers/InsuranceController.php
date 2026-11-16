<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InsuranceClaim;
use App\Models\InsuranceCompany;
use App\Models\JobCard;

class InsuranceController extends Controller
{
    public function index()
    {
        $claims = InsuranceClaim::with(['jobCard', 'vehicle', 'customer', 'insuranceCompany'])
            ->latest('claim_date')
            ->paginate(15);

        return view('insurance.index', compact('claims'));
    }

    public function create()
    {
        $jobCards = JobCard::where('job_type', 'accidental')->orWhere('status', '!=', 'delivered')->with(['vehicle', 'customer'])->get();
        $insuranceCompanies = InsuranceCompany::where('is_active', true)->get();

        return view('insurance.create', compact('jobCards', 'insuranceCompanies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_card_id' => 'required|exists:job_cards,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_number' => 'required|string|max:100',
            'claim_date' => 'required|date',
            'surveyor_name' => 'nullable|string|max:100',
            'surveyor_phone' => 'nullable|string|max:25',
            'estimated_claim_amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $jobCard = JobCard::findOrFail($request->job_card_id);
        $count = InsuranceClaim::whereYear('created_at', date('Y'))->count() + 1;
        $claimNumber = sprintf("CLM-%s-%04d", date('Y'), $count);

        InsuranceClaim::create(array_merge($validated, [
            'claim_number' => $claimNumber,
            'vehicle_id' => $jobCard->vehicle_id,
            'customer_id' => $jobCard->customer_id,
            'status' => 'initiated',
            'approved_claim_amount' => 0.00,
            'customer_liability' => 0.00,
            'insurance_liability' => $validated['estimated_claim_amount'],
        ]));

        return redirect()->route('insurance.index')->with('success', "Insurance Claim #{$claimNumber} registered!");
    }
}
