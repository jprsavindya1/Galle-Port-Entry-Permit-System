<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies with optional search.
     */
    public function index(Request $request)
{
    $companies = Company::when($request->search, function($query, $search) {
        $query->where('name', 'like', "%$search%");
    })->paginate(10);

    if ($request->ajax()) {
        return view('admin.companies._list', compact('companies'))->render();
    }

    return view('admin.companies.index', compact('companies'));
}

public function create(Request $request)
{
    if ($request->ajax()) {
        return view('admin.companies._form', [
            'action' => route('admin.companies.store'),
            'method' => 'POST',
            'company' => new \App\Models\Company
        ])->render();
    }

    return view('admin.companies.create');
}

public function edit(Request $request, Company $company)
{
    if ($request->ajax()) {
        return view('admin.companies._form', [
            'action' => route('admin.companies.update', $company),
            'method' => 'PUT',
            'company' => $company
        ])->render();
    }

    return view('admin.companies.edit', compact('company'));
}
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:500',
    ]);

    Company::create($validated);

    if ($request->ajax()) {
        // After creating, return the updated list view HTML to replace dynamically
        $companies = Company::paginate(10);
        return view('admin.companies._list', compact('companies'))->render();
    }

    return redirect()->route('admin.companies.index')->with('success', 'Company added successfully.');
}
public function update(Request $request, Company $company)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'nullable|string|max:500',
    ]);

    $company->update($validated);

    if ($request->ajax()) {
        // Return updated company list partial (HTML) to replace dynamically
        $companies = Company::paginate(10);
        return view('admin.companies._list', compact('companies'))->render();
    }

    return redirect()->route('admin.companies.index')->with('success', 'Company updated successfully.');
}


public function destroy(Request $request, Company $company)
{
    $company->delete();

    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Company deleted successfully.']);
    }

    return redirect()
        ->route('admin.companies.index')
        ->with('success', 'Company deleted successfully.');
}


}

