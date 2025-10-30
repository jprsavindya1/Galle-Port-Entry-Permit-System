<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
{
    $year = date('Y');
    $month = $request->get('month', date('m'));
    $today = Carbon::today();

    // --- Daily Revenue ---
    $dailyRevenue = Permit::whereDate('created_at', $today)
        ->sum(\DB::raw('`rate` + `vat` + IFNULL(`ssl`,0)'));

    // --- Daily Permits by Type ---
    $dailyTypesData = Permit::select('type', \DB::raw('COUNT(*) as total'))
        ->whereDate('created_at', $today)
        ->groupBy('type')
        ->get()
        ->keyBy('type');

    $dailyPermits = [
        'TP' => $dailyTypesData->has('TP') ? $dailyTypesData['TP']->total : 0,
        'MP' => $dailyTypesData->has('MP') ? $dailyTypesData['MP']->total : 0,
        'VP' => $dailyTypesData->has('VP') ? $dailyTypesData['VP']->total : 0,
    ];

    $dailyPermitsAll = array_sum($dailyPermits);

    // --- Total Permits by Type (Monthly) ---
    $typesData = Permit::select('type', \DB::raw('COUNT(*) as total'))
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy('type')
        ->get()
        ->keyBy('type');

    $totalPermits = [
        'TP' => $typesData->has('TP') ? $typesData['TP']->total : 0,
        'MP' => $typesData->has('MP') ? $typesData['MP']->total : 0,
        'VP' => $typesData->has('VP') ? $typesData['VP']->total : 0,
    ];

    $totalPermitsAll = array_sum($totalPermits);

    // --- Total Monthly Revenue ---
    $totalRevenue = Permit::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum(\DB::raw('`rate` + `vat` + IFNULL(`ssl`,0)'));

    // --- Permits by Company ---
    $companiesData = Permit::select('company_name', \DB::raw('COUNT(*) as total'))
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy('company_name')
        ->orderBy('total', 'desc')
        ->get();

    $companies = $companiesData->pluck('company_name')->toArray();
    $permitCounts = $companiesData->pluck('total')->toArray();

    // --- Permit Type Revenue ---
    $permitTypesData = Permit::select('type', \DB::raw('SUM(`rate` + `vat` + IFNULL(`ssl`,0)) as revenue'))
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy('type')
        ->get();

    $allTypes = ['TP', 'MP', 'VP'];
    $permitRevenue = [];
    foreach ($allTypes as $type) $permitRevenue[$type] = 0;
    foreach ($permitTypesData as $data) $permitRevenue[$data->type] = (float) $data->revenue;

    // --- Months array for dropdown ---
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];

    return view('dashboard', [
        'dailyPermits' => $dailyPermits,
        'dailyPermitsAll' => $dailyPermitsAll,
        'totalPermits' => $totalPermits,
        'totalPermitsAll' => $totalPermitsAll,
        'totalRevenue' => $totalRevenue,
        'dailyRevenue' => $dailyRevenue,
        'companies' => $companies,
        'permitCounts' => $permitCounts,
        'permitTypes' => array_keys($permitRevenue),
        'permitRevenue' => array_values($permitRevenue),
        'months' => $months,
        'selectedMonth' => (int)$month
    ]);
}


   public function getMonthData(Request $request)
{
    $month = $request->get('month', date('m'));
    $year = date('Y');

    // Total Revenue
    $totalRevenue = Permit::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->sum(\DB::raw('`rate` + `vat` + IFNULL(`ssl`,0)'));

    // Total Permits
    $typesData = Permit::select('type', \DB::raw('COUNT(*) as total'))
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy('type')
        ->get()
        ->keyBy('type');

    $totalPermits = [
        'TP' => $typesData->has('TP') ? $typesData['TP']->total : 0,
        'MP' => $typesData->has('MP') ? $typesData['MP']->total : 0,
        'VP' => $typesData->has('VP') ? $typesData['VP']->total : 0,
    ];

    // Permits by Company
    $companiesData = Permit::select('company_name', \DB::raw('COUNT(*) as total'))
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy('company_name')
        ->orderBy('total', 'desc')
        ->get();

    $companies = $companiesData->pluck('company_name');
    $permitCounts = $companiesData->pluck('total');

    // Permit Type Revenue
    $permitTypesData = Permit::select('type', \DB::raw('SUM(`rate` + `vat` + IFNULL(`ssl`,0)) as revenue'))
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->groupBy('type')
        ->get();

    $allTypes = ['TP','MP','VP'];
    $permitRevenue = [];
    foreach($allTypes as $type){
        $permitRevenue[$type] = 0;
    }
    foreach($permitTypesData as $data){
        $permitRevenue[$data->type] = (float) $data->revenue;
    }
    $todayRevenue = Permit::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->whereDate('created_at', Carbon::today())
        ->sum(\DB::raw('`rate` + `vat` + IFNULL(`ssl`,0)'));

    // Daily Permits (always today, regardless of month filter)
    $dailyTypesData = Permit::select('type', \DB::raw('COUNT(*) as total'))
        ->whereDate('created_at', Carbon::today())
        ->groupBy('type')
        ->get()
        ->keyBy('type');

    $dailyPermits = [
        'TP' => $dailyTypesData->has('TP') ? $dailyTypesData['TP']->total : 0,
        'MP' => $dailyTypesData->has('MP') ? $dailyTypesData['MP']->total : 0,
        'VP' => $dailyTypesData->has('VP') ? $dailyTypesData['VP']->total : 0,
    ];

    return response()->json([
        'totalRevenue' => $totalRevenue,
        'totalPermits' => $totalPermits,
        'totalPermitsAll' => array_sum($totalPermits),
        'dailyPermits' => $dailyPermits,
        'dailyPermitsAll' => array_sum($dailyPermits),
        'companies' => $companies,
        'permitCounts' => $permitCounts,
        'permitRevenue' => array_values($permitRevenue),
        'dailyRevenue' => $todayRevenue
    ]);
}
 
}
