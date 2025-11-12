<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemporaryPermit;
use App\Models\MonthlyPermit;  
use App\Models\VehiclePermit;
use App\Models\Payment;
class PrintController extends Controller
{
    /**
     * Display the print view for a given submissions.
     */
    public function show($submission_id)
    {
        // Query all three tables for this submission_id
        $tempPermits = TemporaryPermit::where('submission_id', $submission_id)->get();
        $monthlyPermits = MonthlyPermit::where('submission_id', $submission_id)->get();
        $vehiclePermits = VehiclePermit::where('submission_id', $submission_id)->get();

        // Merge all permits
        $permits = $tempPermits->concat($monthlyPermits)->concat($vehiclePermits);

        $payment = Payment::where('submission_id', $submission_id)->first();

        if ($permits->isEmpty()) {
            abort(404, 'No permits found for this submission.');
        }

        return view('permit.print', compact('permits', 'payment', 'submission_id'));

    }
    public function showSingle($permitType, $id)
{
    // Find the permit based on type
    switch ($permitType) {
        case 'temporary':
            $permit = TemporaryPermit::findOrFail($id);
            break;
        case 'monthly':
            $permit = MonthlyPermit::findOrFail($id);
            break;
        case 'vehicle':
            $permit = VehiclePermit::findOrFail($id);
            break;
        default:
            abort(404, 'Invalid permit type');
    }
    
    $submission_id = $permit->submission_id; 
    $payment = Payment::where('submission_id', $permit->submission_id)->first();

    return view('permit.print_single', compact('permit', 'payment','submission_id'));
}

}
