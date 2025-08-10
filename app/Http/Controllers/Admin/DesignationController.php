<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $designations = Designation::when($request->search, function($q, $search) {
            $q->where('name', 'like', "%$search%");
        })->paginate(10);

        if ($request->ajax()) {
            return view('admin.designations._list', compact('designations'))->render();
        }

        return view('admin.designations.index', compact('designations'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.designations._form', [
                'action' => route('admin.designations.store'),
                'method' => 'POST',
                'designation' => new Designation,
            ])->render();
        }
        return view('admin.designations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:designations,name',
        ]);

        Designation::create($request->only('name'));

        if ($request->ajax()) {
            $designations = Designation::paginate(10);
            return view('admin.designations._list', compact('designations'))->render();
        }

        return redirect()->route('admin.designations.index')->with('success', 'Designation added.');
    }

    public function edit(Request $request, Designation $designation)
    {
        if ($request->ajax()) {
            return view('admin.designations._form', [
                'action' => route('admin.designations.update', $designation),
                'method' => 'PUT',
                'designation' => $designation,
            ])->render();
        }
        return view('admin.designations.edit', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|unique:designations,name,' . $designation->id,
        ]);

        $designation->update($request->only('name'));

        if ($request->ajax()) {
            $designations = Designation::paginate(10);
            return view('admin.designations._list', compact('designations'))->render();
        }

        return redirect()->route('admin.designations.index')->with('success', 'Designation updated.');
    }

    public function destroy(Request $request, Designation $designation)
    {
        $designation->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Designation deleted.']);
        }

        return redirect()->route('admin.designations.index')->with('success', 'Designation deleted.');
    }
}
