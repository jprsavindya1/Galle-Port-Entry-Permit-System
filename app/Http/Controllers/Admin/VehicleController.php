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
        // Get the last vehicle code and generate next one
        $lastVehicle = Vehicle::orderBy('code', 'desc')->first();
        $nextCode = $lastVehicle ? $this->incrementCode($lastVehicle->code) : 'V001';
        
        if ($request->ajax()) {
            return view('admin.vehicles._form', [
                'action' => route('admin.vehicles.store'),
                'method' => 'POST',
                'vehicle' => new Vehicle,
                'nextCode' => $nextCode
            ])->render();
        }

        return view('admin.vehicles.create', compact('nextCode'));
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
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:vehicles,name',
                'regex:/\b(daily|monthly|annually)\b/i'
            ],
            'code' => 'required|string|max:50|unique:vehicles,code',
            'rate' => 'required|numeric|min:0',
        ], [
            'name.regex' => 'Vehicle name must contain one of these words: daily, monthly, or annually'
        ]);

        Vehicle::create($validated);

        $vehicles = Vehicle::paginate(10);
        $html = view('admin.vehicles._list', compact('vehicles'))->render();
        return response()->json(['success' => true, 'message' => 'Vehicle added successfully.', 'html' => $html]);
    }

    // Update an existing vehicle
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:vehicles,name,' . $vehicle->id,
                'regex:/\b(daily|monthly|annually)\b/i'
            ],
            'code' => 'required|string|max:50|unique:vehicles,code,' . $vehicle->id,
            'rate' => 'required|numeric|min:0',
        ], [
            'name.regex' => 'Vehicle name must contain one of these words: daily, monthly, or annually'
        ]);

        $vehicle->update($validated);

        $vehicles = Vehicle::paginate(10);
        $html = view('admin.vehicles._list', compact('vehicles'))->render();
        return response()->json(['success' => true, 'message' => 'Vehicle updated successfully.', 'html' => $html]);
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

    // Helper function to increment vehicle code
    private function incrementCode($code)
    {
        // Extract numeric part from code (e.g., V001 -> 001)
        preg_match('/([A-Za-z]*)([0-9]+)/', $code, $matches);
        
        if (count($matches) >= 3) {
            $prefix = $matches[1];
            $number = intval($matches[2]);
            $padding = strlen($matches[2]);
            
            // Increment and pad with zeros
            $newNumber = str_pad($number + 1, $padding, '0', STR_PAD_LEFT);
            return $prefix . $newNumber;
        }
        
        // Default if pattern doesn't match
        return 'V001';
    }
}
