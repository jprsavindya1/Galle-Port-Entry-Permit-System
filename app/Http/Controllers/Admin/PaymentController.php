<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permit;
use App\Models\TemporaryPermit;
use App\Models\MonthlyPermit;
use App\Models\VehiclePermit;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\Vehicle;
use App\Helpers\IdGeneratorHelper;

class PaymentController extends Controller
{
    public function summary()
    {
        $cart = session('payment_cart', []);
        $submissionId = session('payment_submission_id');

        if (empty($cart) || !$submissionId) {
            return redirect()->route('permit.temporary')->with('error', 'No permit data found for payment.');
        }

        $settings = PaymentSetting::first();
        $baseRateSetting = $settings->rate ?? 0;
        $monthlyRateSetting = $settings->monthly_rate ?? 0;
        $sslRate = $settings->ssl ?? 0;
        $vatRate = $settings->vat ?? 0;
        

        $detailedPayments = [];
        $totalPayment = 0;

        foreach ($cart as $item) {
            $days = \Carbon\Carbon::parse($item['from_date'])->diffInDays($item['to_date']) + 1;

            if ($item['type'] === 'VH') {
                // Vehicle permit: rate comes from vehicles table
                $vehicle = Vehicle::where('name', $item['vehicle_type'])->first();
                $vehicleRate = $vehicle ? $vehicle->rate : 0;

                // Check vehicle type for payment calculation
                $vehicleName = strtolower($item['vehicle_type']);
                if (strpos($vehicleName, 'monthly') !== false) {
                    // For monthly vehicles: payment = rate (no days multiplication)
                    $tRate = $vehicleRate;
                } elseif (strpos($vehicleName, 'annually') !== false) {
                    // For annual vehicles: payment = rate (no days multiplication)
                    $tRate = $vehicleRate;
                } else {
                    // For daily vehicles (or any other): payment = rate * days
                    $tRate = $vehicleRate * $days;
                }
                if ($item['issue_type'] === 'free') {
                    $ssl = 0;
                    $vat = 0;
                    $amount = 0;
                } else {
                    // Original formula: SSL = (tRate / 97.5) * 2.5
                    $sslDivisor = 100 - $sslRate;
                    $dblNSSL = ($tRate / $sslDivisor) * $sslRate;
                    $ssl = round($dblNSSL, 2);
                    
                    // VAT calculation uses unrounded SSL (original formula)
                    $dblVAT = (($tRate + $dblNSSL) / 100) * $vatRate;
                    $vat = round($dblVAT, 2);
                    
                    $amount = round($tRate + $ssl + $vat, 2);
                }

                $totalPayment += $amount;

                $detailedPayments[] = [
                    'entry' => $item,
                    'rate' => $tRate,
                    'ssl' => $ssl,
                    'vat' => $vat,
                    'total' => $amount,
                ];



            } else {
                // Temporary / Monthly permits: rate from settings
                // Monthly permits use fixed rate, Temporary uses rate * days
                if ($item['type'] === 'MP') {
                    $tRate = $monthlyRateSetting; // Fixed rate for monthly (no days multiplication)
                } else {
                    $tRate = $baseRateSetting * $days; // Temporary permits use per-day rate
                }
                
                if ($item['issue_type'] === 'free') {
                    $ssl = 0;
                    $vat = 0;
                    $amount = 0;
                } else {
                    // Original formula: SSL = (tRate / 97.5) * 2.5
                    $sslDivisor = 100 - $sslRate;
                    $dblNSSL = ($tRate / $sslDivisor) * $sslRate;
                    $ssl = round($dblNSSL, 2);
                    
                    // VAT calculation uses unrounded SSL (original formula)
                    $dblVAT = (($tRate + $dblNSSL) / 100) * $vatRate;
                    $vat = round($dblVAT, 2);
                    
                    $amount = round($tRate + $ssl + $vat, 2);
                }

                $totalPayment += $amount;

                $detailedPayments[] = [
                    'entry' => $item,
                    'rate' => $tRate,
                    'ssl' => $ssl,
                    'vat' => $vat,
                    'total' => $amount,
                ];
            }
        }

        return view('payment.summary', compact(
            'cart',
            'submissionId',
            'detailedPayments',
            'totalPayment'
        ));
    }

    public function submit(Request $request)
    {
        $cart = session('payment_cart', []);
        $submissionId = session('payment_submission_id');

        if (empty($cart) || !$submissionId) {
            return redirect()->route('permit.temporary')->with('error', 'No permit data found.');
        }

        $permitType = $cart[0]['type'] ?? 'unknown';
        $settings = PaymentSetting::first();
        $rateSetting = $settings->rate ?? 0;
        $monthlyRateSetting = $settings->monthly_rate ?? 0;
        $sslRate = $settings->ssl ?? 0;
        $vatRate = $settings->vat ?? 0;


        // Save all permits
foreach ($cart as $entry) {
    $entry['permit_id'] = $this->generatePermitId($entry['type']);

    // Move files from temp to permanent storage
    $fileFields = ['photo_path', 'doc_nic_path', 'doc_passport_path', 'doc_driving_licence_path', 'doc_police_report_path'];
    foreach ($fileFields as $field) {
        if (isset($entry[$field]) && !empty($entry[$field]) && strpos($entry[$field], 'temp/') === 0) {
            $tempPath = $entry[$field];
            $filename = basename($tempPath);
            
            // Determine permanent folder name based on field
            $subFolder = ($field === 'photo_path') ? 'photos' : 'docs';
            $permanentPath = "permits/{$subFolder}/{$filename}";
            
            // Move file using Storage facade on the 'public' disk
            if (\Storage::disk('public')->exists($tempPath)) {
                \Storage::disk('public')->move($tempPath, $permanentPath);
                $entry[$field] = $permanentPath;
            }
        }
    }

    // Safely handle pass_type: only for TP/MP
    if (isset($entry['pass_type'])) {
        $entry['pass_type'] = is_array($entry['pass_type']) ? implode(',', $entry['pass_type']) : $entry['pass_type'];
    } else {
        $entry['pass_type'] = null;
    }

    // ===== Calculate per-permit totals =====
    $days = \Carbon\Carbon::parse($entry['from_date'])->diffInDays(\Carbon\Carbon::parse($entry['to_date'])) + 1;

    if ($entry['type'] === 'VH') {
        $vehicle = Vehicle::where('name', $entry['vehicle_type'])->first();
        $vehicleRate = $vehicle ? $vehicle->rate : 0;
        
        // Check vehicle type for payment calculation
        $vehicleName = strtolower($entry['vehicle_type']);
        if (strpos($vehicleName, 'monthly') !== false) {
            // For monthly vehicles: payment = rate (no days multiplication)
            $rate = $vehicleRate;
        } elseif (strpos($vehicleName, 'annually') !== false) {
            // For annual vehicles: payment = rate (no days multiplication)
            $rate = $vehicleRate;
        } else {
            // For daily vehicles (or any other): payment = rate * days
            $rate = $vehicleRate * $days;
        }
    } elseif ($entry['type'] === 'MP') {
        $rate = $monthlyRateSetting; // Fixed rate for monthly (no days multiplication)
    } else {
        $rate = $rateSetting * $days; // Temporary permits use per-day rate
    }

    if ($entry['issue_type'] === 'free') {
        $ssl = 0;
        $vat = 0;
        $total = 0;
    } else {
        // Use same formula as summary page for consistency
        $sslDivisor = 100 - $sslRate;
        $dblNSSL = ($rate / $sslDivisor) * $sslRate;
        $ssl = round($dblNSSL, 2);
        
        $dblVAT = (($rate + $dblNSSL) / 100) * $vatRate;
        $vat = round($dblVAT, 2);
        
        $total = round($rate + $ssl + $vat, 2);
    }

    // Add totals to $entry
    $entry['rate'] = $rate;
    $entry['ssl'] = $ssl;
    $entry['vat'] = $vat;
    $entry['total'] = $total;
    $entry['status'] = 'active'; // Set status to active when permit is created
    
    // Ensure owner_address is not null
    $entry['owner_address'] = $entry['owner_address'] ?? '';
    
    // Ensure document checkboxes default to 0 if not present
    $entry['doc_nic'] = $entry['doc_nic'] ?? 0;
    $entry['doc_passport'] = $entry['doc_passport'] ?? 0;
    $entry['doc_driving_licence'] = $entry['doc_driving_licence'] ?? 0;
    $entry['doc_police_report'] = $entry['doc_police_report'] ?? 0;
    $entry['doc_revenue_licence'] = $entry['doc_revenue_licence'] ?? 0;
    $entry['doc_insurance'] = $entry['doc_insurance'] ?? 0;
    // ======================================

    // Save to the appropriate table based on permit type
    switch ($entry['type']) {
        case 'TP':
            TemporaryPermit::create($entry);
            break;
        case 'MP':
            MonthlyPermit::create($entry);
            break;
        case 'VH':
            VehiclePermit::create($entry);
            break;
    }
    
    // Also save to old permits table for backward compatibility
    Permit::create($entry);
}


        // Calculate totals
        $entryCount = count($cart); // Count all entries (both free and payment)
        $rateTotal = 0;
        $sslTotal = 0;
        $vatTotal = 0;

        foreach ($cart as $entry) {
            if ($entry['issue_type'] !== 'free') {
                $days = \Carbon\Carbon::parse($entry['from_date'])->diffInDays($entry['to_date']) + 1;

               if ($permitType === 'VH') {
                    // Vehicle rate
                    $vehicle = Vehicle::where('name', $entry['vehicle_type'])->first();
                    $vehicleRate = $vehicle ? $vehicle->rate : 0;
                    
                    // Check vehicle type for payment calculation
                    $vehicleName = strtolower($entry['vehicle_type']);
                    if (strpos($vehicleName, 'monthly') !== false) {
                        // For monthly vehicles: payment = rate (no days multiplication)
                        $baseRate = $vehicleRate;
                    } elseif (strpos($vehicleName, 'annually') !== false) {
                        // For annual vehicles: payment = rate (no days multiplication)
                        $baseRate = $vehicleRate;
                    } else {
                        // For daily vehicles (or any other): payment = rate * days
                        $baseRate = $vehicleRate * $days;
                    }

                    // Use same formula as summary page for consistency
                    $sslDivisor = 100 - $sslRate;
                    $dblNSSL = ($baseRate / $sslDivisor) * $sslRate;
                    $ssl = round($dblNSSL, 2);
                    
                    $dblVAT = (($baseRate + $dblNSSL) / 100) * $vatRate;
                    $vat = round($dblVAT, 2);

                    $rateTotal += $baseRate;
                    $sslTotal += $ssl;
                    $vatTotal += $vat;

                } elseif ($permitType === 'MP') {
                    // Monthly permits use fixed rate
                    $baseRate = $monthlyRateSetting; // Fixed rate (no days multiplication)

                    // Use same formula as summary page for consistency
                    $sslDivisor = 100 - $sslRate;
                    $dblNSSL = ($baseRate / $sslDivisor) * $sslRate;
                    $ssl = round($dblNSSL, 2);
                    
                    $dblVAT = (($baseRate + $dblNSSL) / 100) * $vatRate;
                    $vat = round($dblVAT, 2);

                    $rateTotal += $baseRate;
                    $sslTotal += $ssl;
                    $vatTotal += $vat;

                } else {
                    // Temporary permits from settings (per-day rate)
                    $baseRate = $rateSetting * $days;

                    // Use same formula as summary page for consistency
                    $sslDivisor = 100 - $sslRate;
                    $dblNSSL = ($baseRate / $sslDivisor) * $sslRate;
                    $ssl = round($dblNSSL, 2);
                    
                    $dblVAT = (($baseRate + $dblNSSL) / 100) * $vatRate;
                    $vat = round($dblVAT, 2);

                    $rateTotal += $baseRate;
                    $sslTotal += $ssl;
                    $vatTotal += $vat;
                }
            }
        }

        // Generate collision-free invoice ID with permit type
        $invoiceId = IdGeneratorHelper::generateInvoiceId($permitType);

        // Total amount
      $amountTotal = $rateTotal + $sslTotal + $vatTotal;


        // Save to payments table
        Payment::create([
            'submission_id' => $submissionId,
            'invoice_id' => $invoiceId,
            'permit_type' => $permitType,
            'entry_count' => $entryCount,
            'rate_total' => $rateTotal,
            'ssl_total' => $sslTotal,
            'vat_total' => $vatTotal,
            'amount_total' => $amountTotal,
            'status' => 'Paid',
            'payment_date' => now(),
            'paid_at' => now(),
            
        ]);

        // Clear sessions
        session()->forget([
            'permit_cart',
            'payment_cart',
            'payment_submission_id',
            'temporary_company_name',
            'temporary_company_address',
            'monthly_company_name',
            'monthly_company_address',
            'monthly_permit_cart',
            'temporary_permit_cart',
            'vehicle_permit_cart',
        ]);

        return redirect()->route('payment.invoice', ['submission_id' => $submissionId]);
    }

    private function generatePermitId($type)
    {
        $prefix = $type;
        $yearMonth = now()->format('ym');

        // Check both the new separate table AND the old permits table to avoid duplicates
        switch ($type) {
            case 'TP':
                $latestNew = TemporaryPermit::where('permit_id', 'like', $prefix . $yearMonth . '%')
                    ->orderBy('permit_id', 'desc')
                    ->first();
                break;
            case 'MP':
                $latestNew = MonthlyPermit::where('permit_id', 'like', $prefix . $yearMonth . '%')
                    ->orderBy('permit_id', 'desc')
                    ->first();
                break;
            case 'VH':
                $latestNew = VehiclePermit::where('permit_id', 'like', $prefix . $yearMonth . '%')
                    ->orderBy('permit_id', 'desc')
                    ->first();
                break;
            default:
                $latestNew = null;
                break;
        }

        // Also check the old permits table
        $latestOld = Permit::where('permit_id', 'like', $prefix . $yearMonth . '%')
            ->orderBy('permit_id', 'desc')
            ->first();

        // Get the highest number from both tables
        $nextNumber = 1;
        
        if ($latestNew) {
            $lastCounter = (int)substr($latestNew->permit_id, -4);
            $nextNumber = max($nextNumber, $lastCounter + 1);
        }
        
        if ($latestOld) {
            $lastCounter = (int)substr($latestOld->permit_id, -4);
            $nextNumber = max($nextNumber, $lastCounter + 1);
        }

        return $prefix . $yearMonth . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function submitPayment(Request $request)
    {
        $cart = session()->get('permit_cart', []);
        $submissionId = session()->get('submission_id');
        $totalAmount = session()->get('payment_total');

        if (empty($cart) || !$submissionId || !$totalAmount) {
            return redirect()->route('permit.temporary')->with('error', 'Session expired or invalid.');
        }

        // Get permit type from cart
        $permitType = $cart[0]['type'] ?? 'TP';

        // Generate collision-free invoice ID with permit type
        $invoiceId = IdGeneratorHelper::generateInvoiceId($permitType);

        Payment::create([
            'submission_id' => $submissionId,
            'invoice_id' => $invoiceId,
            'amount_total' => $totalAmount,
            'rate_total' => 0,
            'ssl_total' => 0,
            'vat_total' => 0,
            'entry_count' => count($cart),
            'permit_type' => $cart[0]['type'] ?? 'unknown',
            'status' => 'Paid',
            'payment_date' => now(),
            'paid_at' => now(),
        ]);

        session()->forget(['permit_cart', 'company_name', 'company_address', 'submission_id', 'payment_total']);

        return redirect()->route('payment.invoice', ['submission_id' => $submissionId]);
    }

    public function invoice($submission_id)
    {
        $payment = Payment::where('submission_id', $submission_id)->firstOrFail();
        
        // Fetch permits from all three tables and merge them
        $temporaryPermits = TemporaryPermit::where('submission_id', $submission_id)->get();
        $monthlyPermits = MonthlyPermit::where('submission_id', $submission_id)->get();
        $vehiclePermits = VehiclePermit::where('submission_id', $submission_id)->get();
        
        // Add type attribute to each permit for identification
        $temporaryPermits->each(function($permit) {
            $permit->type = 'TP';
        });
        $monthlyPermits->each(function($permit) {
            $permit->type = 'MP';
        });
        $vehiclePermits->each(function($permit) {
            $permit->type = 'VH';
        });
        
        // Merge all permits into one collection
        $permits = $temporaryPermits->concat($monthlyPermits)->concat($vehiclePermits);

        return view('payment.invoice', compact('payment', 'permits', 'submission_id'));
    }
}
