<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    // Display a listing of vehicles with optional search
    public function index(Request $request)
    {
        $vehicles = Vehicle::when($request->search, function($query, $search) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
        })->paginate(10);

        if ($request->ajax()) {
            return view('admin.vehicles._list', compact('vehicles'))->render();
        }

        return view('admin.vehicles.index', compact('vehicles'));
    }

    // Show the create form
    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.vehicles._form', [
                'action' => route('admin.vehicles.store'),
                'method' => 'POST',
                'vehicle' => new Vehicle
            ])->render();
        }

        return view('admin.vehicles.create');
    }

    // Show the edit form
    public function edit(Request $request, Vehicle $vehicle)
    {
        if ($request->ajax()) {
            return view('admin.vehicles._form', [
                'action' => route('admin.vehicles.update', $vehicle),
                'method' => 'PUT',
                'vehicle' => $vehicle
            ])->render();
        }

        return view('admin.vehicles.edit', compact('vehicle'));
    }

    // Store a new vehicle
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vehicles,name',
            'code' => 'required|string|max:50|unique:vehicles,code',
            'rate' => 'required|numeric|min:0',
        ]);

        Vehicle::create($validated);

        if ($request->ajax()) {
            $vehicles = Vehicle::paginate(10);
            return view('admin.vehicles._list', compact('vehicles'))->render();
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle added successfully.');
    }

    // Update an existing vehicle
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vehicles,name,' . $vehicle->id,
            'code' => 'required|string|max:50|unique:vehicles,code,' . $vehicle->id,
            'rate' => 'required|numeric|min:0',
        ]);

        $vehicle->update($validated);

        if ($request->ajax()) {
            $vehicles = Vehicle::paginate(10);
            return view('admin.vehicles._list', compact('vehicles'))->render();
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    // Delete a vehicle
    public function destroy(Request $request, Vehicle $vehicle)
    {
        $vehicle->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Vehicle deleted successfully.']);
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
