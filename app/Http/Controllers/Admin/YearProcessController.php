<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\TemporaryPermit;
use App\Models\MonthlyPermit;
use App\Models\VehiclePermit;
use Illuminate\Support\Facades\DB;

class YearProcessController extends Controller
{
    /**
     * Display the Year & Process dashboard.
     */
    public function index()
    {
        $activeYear = SystemSetting::where('key', 'active_year')->value('value') ?? date('Y');
        $resetCycle = SystemSetting::where('key', 'permit_id_reset_cycle')->value('value') ?? 'yearly';

        $yearSuffix = substr($activeYear, -2);

        // Fetch counts for the active year
        $tpCount = TemporaryPermit::where('permit_id', 'like', 'TP' . $yearSuffix . '%')->count();
        $mpCount = MonthlyPermit::where('permit_id', 'like', 'MP' . $yearSuffix . '%')->count();
        $vhCount = VehiclePermit::where('permit_id', 'like', 'VH' . $yearSuffix . '%')->count();

        // Fetch highest sequence number used in the active year
        $tpHighest = TemporaryPermit::where('permit_id', 'like', 'TP' . $yearSuffix . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(permit_id, -4) AS UNSIGNED)) as max_counter')
            ->first()->max_counter ?? 0;

        $mpHighest = MonthlyPermit::where('permit_id', 'like', 'MP' . $yearSuffix . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(permit_id, -4) AS UNSIGNED)) as max_counter')
            ->first()->max_counter ?? 0;

        $vhHighest = VehiclePermit::where('permit_id', 'like', 'VH' . $yearSuffix . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(permit_id, -4) AS UNSIGNED)) as max_counter')
            ->first()->max_counter ?? 0;

        $stats = [
            'tp' => ['count' => $tpCount, 'highest' => str_pad($tpHighest, 4, '0', STR_PAD_LEFT)],
            'mp' => ['count' => $mpCount, 'highest' => str_pad($mpHighest, 4, '0', STR_PAD_LEFT)],
            'vh' => ['count' => $vhCount, 'highest' => str_pad($vhHighest, 4, '0', STR_PAD_LEFT)],
        ];

        return view('admin.year_process.index', compact('activeYear', 'resetCycle', 'stats'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'active_year' => 'required|integer|min:2020|max:2099',
            'permit_id_reset_cycle' => 'required|in:yearly,monthly',
        ]);

        SystemSetting::updateOrCreate(
            ['key' => 'active_year'],
            ['value' => $request->active_year]
        );

        SystemSetting::updateOrCreate(
            ['key' => 'permit_id_reset_cycle'],
            ['value' => $request->permit_id_reset_cycle]
        );

        return redirect()->route('admin.year_process.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Transition to the next year automatically.
     */
    public function startNewYear()
    {
        $currentYear = (int)(SystemSetting::where('key', 'active_year')->value('value') ?? date('Y'));
        $nextYear = $currentYear + 1;

        SystemSetting::updateOrCreate(
            ['key' => 'active_year'],
            ['value' => $nextYear]
        );

        return redirect()->route('admin.year_process.index')
            ->with('success', "Transitioned successfully to Year {$nextYear}! All new permits will now start from sequence 0001.");
    }
}
