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

        // If this is a batch of TEMPORARY permits (TP), export and serve them using Crystal Reports PDF
        if ($tempPermits->isNotEmpty() && $monthlyPermits->isEmpty() && $vehiclePermits->isEmpty()) {
            try {
                $scriptPath = storage_path('reports/crystal_print.vbs');
                
                // Construct a comma-separated list of permit IDs
                $permitIds = $tempPermits->pluck('permit_id')->implode(',');
                
                // Unique PDF path for this batch submission
                $pdfPath = storage_path('reports/batch_print_' . $submission_id . '.pdf');
                $cscriptPath = 'C:\\Windows\\SysWOW64\\cscript.exe';
                
                if (!file_exists($cscriptPath)) {
                    throw new \Exception("32-bit Windows Script Host (cscript.exe) is missing at: " . $cscriptPath);
                }
                
                if (file_exists($pdfPath)) {
                    @unlink($pdfPath);
                }

                // Run VBScript to export the batch of permits to PDF (pass "TP")
                $command = sprintf('"%s" //NoLogo "%s" "TP" "%s" "%s"', $cscriptPath, $scriptPath, $permitIds, $pdfPath);
                $output = shell_exec($command);
                
                if (!$output || stripos($output, 'SUCCESS') === false) {
                    throw new \Exception("Crystal Reports Batch PDF Export failed: " . ($output ?: 'No response'));
                }
                
                // Mark all temporary permits as printed
                $userId = auth()->id();
                $now = now();
                foreach ($tempPermits as $permit) {
                    $permit->update([
                        'is_printed' => true,
                        'printed_at' => $now,
                        'printed_by' => $userId,
                    ]);
                }

                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="permits_' . $submission_id . '.pdf"'
                ]);

            } catch (\Exception $e) {
                \Log::error("Crystal Reports Batch PDF Print Error: " . $e->getMessage());
                // Fallback to original HTML batch view if Crystal Reports fails
            }
        }

        // If this is a batch of MONTHLY permits (MP), export and serve them using Crystal Reports PDF
        if ($monthlyPermits->isNotEmpty() && $tempPermits->isEmpty() && $vehiclePermits->isEmpty()) {
            try {
                $scriptPath = storage_path('reports/crystal_print.vbs');
                
                // Construct a comma-separated list of permit IDs
                $permitIds = $monthlyPermits->pluck('permit_id')->implode(',');
                
                // Unique PDF path for this batch submission
                $pdfPath = storage_path('reports/batch_print_' . $submission_id . '.pdf');
                $cscriptPath = 'C:\\Windows\\SysWOW64\\cscript.exe';
                
                if (!file_exists($cscriptPath)) {
                    throw new \Exception("32-bit Windows Script Host (cscript.exe) is missing at: " . $cscriptPath);
                }
                
                if (file_exists($pdfPath)) {
                    @unlink($pdfPath);
                }

                // Run VBScript to export the batch of monthly permits to PDF (pass "MP")
                $command = sprintf('"%s" //NoLogo "%s" "MP" "%s" "%s"', $cscriptPath, $scriptPath, $permitIds, $pdfPath);
                $output = shell_exec($command);
                
                if (!$output || stripos($output, 'SUCCESS') === false) {
                    throw new \Exception("Crystal Reports Batch PDF Export failed: " . ($output ?: 'No response'));
                }
                
                // Mark all monthly permits as printed
                $userId = auth()->id();
                $now = now();
                foreach ($monthlyPermits as $permit) {
                    $permit->update([
                        'is_printed' => true,
                        'printed_at' => $now,
                        'printed_by' => $userId,
                    ]);
                }

                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="permits_' . $submission_id . '.pdf"'
                ]);

            } catch (\Exception $e) {
                \Log::error("Crystal Reports Batch PDF Print Error: " . $e->getMessage());
                // Fallback to original HTML batch view if Crystal Reports fails
            }
        }

        // If this is a batch of VEHICLE permits (VH), export and serve them using Crystal Reports PDF
        if ($vehiclePermits->isNotEmpty() && $tempPermits->isEmpty() && $monthlyPermits->isEmpty()) {
            try {
                $scriptPath = storage_path('reports/crystal_print.vbs');
                
                // Construct a comma-separated list of permit IDs
                $permitIds = $vehiclePermits->pluck('permit_id')->implode(',');
                
                // Unique PDF path for this batch submission
                $pdfPath = storage_path('reports/batch_print_' . $submission_id . '.pdf');
                $cscriptPath = 'C:\\Windows\\SysWOW64\\cscript.exe';
                
                if (!file_exists($cscriptPath)) {
                    throw new \Exception("32-bit Windows Script Host (cscript.exe) is missing at: " . $cscriptPath);
                }
                
                if (file_exists($pdfPath)) {
                    @unlink($pdfPath);
                }

                $tempId = \DB::table('temporary_permits')->value('id') ?: 1;
                $monthlyId = \DB::table('monthly_permits')->value('id') ?: 1;

                // Run VBScript to export the batch of vehicle permits to PDF (pass "VH" along with table constraint IDs)
                $command = sprintf('"%s" //NoLogo "%s" "VH" "%s" "%s" "%d" "%d"', $cscriptPath, $scriptPath, $permitIds, $pdfPath, $tempId, $monthlyId);
                $output = shell_exec($command);
                
                if (!$output || stripos($output, 'SUCCESS') === false) {
                    throw new \Exception("Crystal Reports Batch PDF Export failed: " . ($output ?: 'No response'));
                }
                
                // Mark all vehicle permits as printed
                $userId = auth()->id();
                $now = now();
                foreach ($vehiclePermits as $permit) {
                    $permit->update([
                        'is_printed' => true,
                        'printed_at' => $now,
                        'printed_by' => $userId,
                    ]);
                }

                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="permits_' . $submission_id . '.pdf"'
                ]);

            } catch (\Exception $e) {
                \Log::error("Crystal Reports Batch PDF Print Error: " . $e->getMessage());
                // Fallback to original HTML batch view if Crystal Reports fails
            }
        }

        // Mark all permits as printed
        $userId = auth()->id();
        $now = now();
        
        foreach ($tempPermits as $permit) {
            $permit->update([
                'is_printed' => true,
                'printed_at' => $now,
                'printed_by' => $userId,
            ]);
        }
        
        foreach ($monthlyPermits as $permit) {
            $permit->update([
                'is_printed' => true,
                'printed_at' => $now,
                'printed_by' => $userId,
            ]);
        }
        
        foreach ($vehiclePermits as $permit) {
            $permit->update([
                'is_printed' => true,
                'printed_at' => $now,
                'printed_by' => $userId,
            ]);
        }

        return view('permit.print', compact('permits', 'payment', 'submission_id'));
    }
    public function showSingle($type, $id)
{
    // Find the permit based on type
    switch ($type) {
        case 'TP':
            $permit = TemporaryPermit::findOrFail($id);
            
            // Retrieve submission and payment details for logging/fallback
            $submission_id = $permit->submission_id; 
            $payment = Payment::where('submission_id', $permit->submission_id)->first();
            
            try {
                $scriptPath = storage_path('reports/crystal_print.vbs');
                $pdfPath = storage_path('reports/permit_' . $permit->permit_id . '.pdf');
                $cscriptPath = 'C:\\Windows\\SysWOW64\\cscript.exe';
                
                if (!file_exists($cscriptPath)) {
                    throw new \Exception("32-bit Windows Script Host (cscript.exe) is missing at: " . $cscriptPath);
                }
                
                if (!file_exists($scriptPath)) {
                    throw new \Exception("Print helper script not found at: " . $scriptPath);
                }

                // If a PDF already exists for this permit, delete it first to avoid stale data
                if (file_exists($pdfPath)) {
                    @unlink($pdfPath);
                }

                // Execute the 32-bit cscript in the background to export the report to PDF (pass "TP")
                $command = sprintf('"%s" //NoLogo "%s" "TP" "%s" "%s"', $cscriptPath, $scriptPath, $permit->permit_id, $pdfPath);
                $output = shell_exec($command);
                
                if (!$output || stripos($output, 'SUCCESS') === false) {
                    throw new \Exception("Crystal Reports PDF Export failed: " . ($output ?: 'No response'));
                }
                
                if (!file_exists($pdfPath)) {
                    throw new \Exception("PDF file was not created by the export helper.");
                }
                
                // Mark permit as printed
                $permit->update([
                    'is_printed' => true,
                    'printed_at' => now(),
                    'printed_by' => auth()->id(),
                ]);
                
                // Serve the PDF file inline to the browser so the user can preview and print it
                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="permit_' . $permit->permit_id . '.pdf"'
                ]);
                
            } catch (\Exception $e) {
                \Log::error("Crystal Reports PDF Print Error: " . $e->getMessage());
                // Fallback to original HTML/CSS print view if Crystal Reports fails
                return view('permit.print_single', compact('permit', 'payment', 'submission_id'));
            }
            break;
            
        case 'MP':
            $permit = MonthlyPermit::findOrFail($id);
            
            // Retrieve submission and payment details for logging/fallback
            $submission_id = $permit->submission_id; 
            $payment = Payment::where('submission_id', $permit->submission_id)->first();
            
            try {
                $scriptPath = storage_path('reports/crystal_print.vbs');
                $pdfPath = storage_path('reports/permit_' . $permit->permit_id . '.pdf');
                $cscriptPath = 'C:\\Windows\\SysWOW64\\cscript.exe';
                
                if (!file_exists($cscriptPath)) {
                    throw new \Exception("32-bit Windows Script Host (cscript.exe) is missing at: " . $cscriptPath);
                }
                
                if (!file_exists($scriptPath)) {
                    throw new \Exception("Print helper script not found at: " . $scriptPath);
                }

                // If a PDF already exists for this permit, delete it first to avoid stale data
                if (file_exists($pdfPath)) {
                    @unlink($pdfPath);
                }

                // Execute the 32-bit cscript in the background to export the report to PDF (pass "MP")
                $command = sprintf('"%s" //NoLogo "%s" "MP" "%s" "%s"', $cscriptPath, $scriptPath, $permit->permit_id, $pdfPath);
                $output = shell_exec($command);
                
                if (!$output || stripos($output, 'SUCCESS') === false) {
                    throw new \Exception("Crystal Reports PDF Export failed: " . ($output ?: 'No response'));
                }
                
                if (!file_exists($pdfPath)) {
                    throw new \Exception("PDF file was not created by the export helper.");
                }
                
                // Mark permit as printed
                $permit->update([
                    'is_printed' => true,
                    'printed_at' => now(),
                    'printed_by' => auth()->id(),
                ]);
                
                // Serve the PDF file inline to the browser so the user can preview and print it
                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="permit_' . $permit->permit_id . '.pdf"'
                ]);
                
            } catch (\Exception $e) {
                \Log::error("Crystal Reports Monthly PDF Print Error: " . $e->getMessage());
                // Fallback to original HTML/CSS print view if Crystal Reports fails
                return view('permit.print_single', compact('permit', 'payment', 'submission_id'));
            }
            break;
            
        case 'VH':
            $permit = VehiclePermit::findOrFail($id);
            
            // Retrieve submission and payment details for logging/fallback
            $submission_id = $permit->submission_id; 
            $payment = Payment::where('submission_id', $permit->submission_id)->first();
            
            try {
                $scriptPath = storage_path('reports/crystal_print.vbs');
                $pdfPath = storage_path('reports/permit_' . $permit->permit_id . '.pdf');
                $cscriptPath = 'C:\\Windows\\SysWOW64\\cscript.exe';
                
                if (!file_exists($cscriptPath)) {
                    throw new \Exception("32-bit Windows Script Host (cscript.exe) is missing at: " . $cscriptPath);
                }
                
                if (!file_exists($scriptPath)) {
                    throw new \Exception("Print helper script not found at: " . $scriptPath);
                }

                // If a PDF already exists for this permit, delete it first to avoid stale data
                if (file_exists($pdfPath)) {
                    @unlink($pdfPath);
                }

                $tempId = \DB::table('temporary_permits')->value('id') ?: 1;
                $monthlyId = \DB::table('monthly_permits')->value('id') ?: 1;

                // Execute the 32-bit cscript in the background to export the report to PDF (pass "VH" along with table constraint IDs)
                $command = sprintf('"%s" //NoLogo "%s" "VH" "%s" "%s" "%d" "%d"', $cscriptPath, $scriptPath, $permit->permit_id, $pdfPath, $tempId, $monthlyId);
                $output = shell_exec($command);
                
                if (!$output || stripos($output, 'SUCCESS') === false) {
                    throw new \Exception("Crystal Reports PDF Export failed: " . ($output ?: 'No response'));
                }
                
                if (!file_exists($pdfPath)) {
                    throw new \Exception("PDF file was not created by the export helper.");
                }
                
                // Mark permit as printed
                $permit->update([
                    'is_printed' => true,
                    'printed_at' => now(),
                    'printed_by' => auth()->id(),
                ]);
                
                // Serve the PDF file inline to the browser so the user can preview and print it
                return response()->file($pdfPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="permit_' . $permit->permit_id . '.pdf"'
                ]);
                
            } catch (\Exception $e) {
                \Log::error("Crystal Reports Vehicle PDF Print Error: " . $e->getMessage());
                // Fallback to original HTML/CSS print view if Crystal Reports fails
                
                $permit->update([
                    'is_printed' => true,
                    'printed_at' => now(),
                    'printed_by' => auth()->id(),
                ]);
                
                return view('permit.print_single', compact('permit', 'payment', 'submission_id'));
            }
            break;
            
        default:
            abort(404, 'Invalid permit type');
    }
}

}
