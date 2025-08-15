<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit;
use App\Models\Payment;
class PrintController extends Controller
{
    /**
     * Display the print view for a given submission.
     */
    public function show($submission_id)
    {
        $permits = Permit::where('submission_id', $submission_id)->get();
        $payment = Payment::where('submission_id', $submission_id)->first();

        if ($permits->isEmpty()) {
            abort(404, 'No permits found for this submission.');
        }

        return view('permit.print', compact('permits', 'payment', 'submission_id'));

    }
    public function showSingle($id)
{
    $permit = Permit::findOrFail($id);
     $submission_id = $permit->submission_id; 
    $payment = Payment::where('submission_id', $permit->submission_id)->first();

    return view('permit.print_single', compact('permit', 'payment','submission_id'));
}

}
