<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = date('Y');
        $month = $request->get('month', date('m')); // Month filter from request, default current month

        // --- Total Permits by Type ---
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

        // --- Total Revenue for selected month ---
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

        // --- Permit Type Distribution & Revenue ---
        $permitTypesData = Permit::select('type', \DB::raw('SUM(`rate` + `vat` + IFNULL(`ssl`,0)) as revenue'))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('type')
            ->get();

        $allTypes = ['TP', 'MP', 'VP'];
        $permitRevenue = [];
        foreach ($allTypes as $type) {
            $permitRevenue[$type] = 0;
        }
        foreach ($permitTypesData as $data) {
            $permitRevenue[$data->type] = (float) $data->revenue;
        }

        // --- Months array for dropdown ---
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return view('dashboard', [
            'totalPermits' => $totalPermits,
            'totalPermitsAll' => $totalPermitsAll,
            'totalRevenue' => $totalRevenue,
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

    return response()->json([
        'totalRevenue' => $totalRevenue,
        'totalPermits' => $totalPermits,
        'totalPermitsAll' => array_sum($totalPermits),
        'companies' => $companies,
        'permitCounts' => $permitCounts,
        'permitRevenue' => array_values($permitRevenue),
    ]);
}
 
}
