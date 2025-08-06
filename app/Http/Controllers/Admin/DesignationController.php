<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::all();
        return view('admin.designations.index', compact('designations'));
    }

    public function create()
    {
        return view('admin.designations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:designations,name',
        ]);

        Designation::create($request->only('name'));
        return redirect()->route('admin.designations.index')->with('success', 'Designation added.');
    }

    public function edit(Designation $designation)
    {
        return view('admin.designations.edit', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|unique:designations,name,' . $designation->id,
        ]);

        $designation->update($request->only('name'));
        return redirect()->route('admin.designations.index')->with('success', 'Designation updated.');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();
        return redirect()->route('admin.designations.index')->with('success', 'Designation deleted.');
    }
}
