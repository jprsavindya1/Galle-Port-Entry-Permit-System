<?php

namespace App\Http\Controllers;

use App\Models\Permit;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    /**
     * Show the security dashboard with simple permit search
     */
    public function index()
    {
        return view('security.dashboard');
    }

    /**
     * Search for permit by permit ID
     */
    public function searchPermit(Request $request)
    {
        $request->validate([
            'permit_id' => 'required|string'
        ]);

        $permitId = strtoupper(trim($request->permit_id));
        
        // Search for the permit
        $permit = Permit::where('permit_id', $permitId)
            ->with('payment')
            ->first();

        if (!$permit) {
            return response()->json([
                'success' => false,
                'message' => 'Permit not found. Please check the Permit ID and try again.'
            ]);
        }

        // Check if permit is valid (active and within date range)
        $today = now()->format('Y-m-d');
        $isValid = false;
        $validityMessage = '';

        if ($permit->status === 'cancelled') {
            $validityMessage = 'CANCELLED - This permit has been cancelled';
        } elseif ($permit->status === 'active') {
            if ($permit->from_date && $permit->to_date) {
                if ($today >= $permit->from_date && $today <= $permit->to_date) {
                    $isValid = true;
                    $validityMessage = 'VALID - Permit is active and within validity period';
                } elseif ($today < $permit->from_date) {
                    $validityMessage = 'NOT YET VALID - Permit starts on ' . date('d M Y', strtotime($permit->from_date));
                } else {
                    $validityMessage = 'EXPIRED - Permit expired on ' . date('d M Y', strtotime($permit->to_date));
                }
            } else {
                $isValid = true;
                $validityMessage = 'VALID - Permit is active';
            }
        } else {
            $validityMessage = 'INACTIVE - Permit status: ' . ucfirst($permit->status);
        }

        return response()->json([
            'success' => true,
            'permit' => [
                'permit_id' => $permit->permit_id,
                'full_name' => $permit->full_name,
                'id_type' => strtoupper($permit->id_type),
                'id_number' => $permit->id_number,
                'from_date' => $permit->from_date ? date('d M Y', strtotime($permit->from_date)) : 'N/A',
                'to_date' => $permit->to_date ? date('d M Y', strtotime($permit->to_date)) : 'N/A',
                'status' => ucfirst($permit->status),
                'is_valid' => $isValid,
                'validity_message' => $validityMessage,
                'vehicle_number' => $permit->vehicle_number,
                'vehicle_type' => $permit->vehicle_type,
                'company_name' => $permit->company_name,
                'type' => ucfirst($permit->type),
            ]
        ]);
    }
}
