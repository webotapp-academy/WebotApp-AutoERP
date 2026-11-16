<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Branch;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'approver']);

        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        $expenses = $query->latest('expense_date')->paginate(15)->withQueryString();
        $categories = ExpenseCategory::all();
        $totalExpenses = Expense::sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'totalExpenses'));
    }

    public function create()
    {
        $categories = ExpenseCategory::all();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
            'payee' => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);

        $branch = Branch::first();
        $count = Expense::whereYear('created_at', date('Y'))->count() + 1;
        $expNumber = sprintf("EXP-%s-%04d", date('Y'), $count);

        Expense::create(array_merge($validated, [
            'expense_number' => $expNumber,
            'branch_id' => $branch->id ?? 1,
            'approved_by' => auth()->id(),
        ]));

        return redirect()->route('expenses.index')->with('success', "Expense #{$expNumber} recorded!");
    }
}
