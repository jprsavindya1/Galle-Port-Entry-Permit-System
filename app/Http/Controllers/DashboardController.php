<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemporaryPermit;
use App\Models\MonthlyPermit;
use App\Models\VehiclePermit;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Redirect security role to their specific dashboard
        if (auth()->check() && auth()->user()->role === 'security') {
            return redirect()->route('security.dashboard');
        }

        $year = date('Y');
        $month = $request->get('month', date('m'));
        $today = Carbon::today();

        // --- Daily Revenue (from payments table - source of truth) ---
        $dailyRevenue = Payment::where('status', 'Paid')
            ->whereDate('payment_date', $today)
            ->sum('amount_total');

        // --- Daily Permits by Type ---
        $dailyPermits = [
            'TP' => TemporaryPermit::whereDate('created_at', $today)->count(),
            'MP' => MonthlyPermit::whereDate('created_at', $today)->count(),
            'VH' => VehiclePermit::whereDate('created_at', $today)->count(),
        ];

        $dailyPermitsAll = array_sum($dailyPermits);

        // --- Total Permits by Type (Monthly) ---
        $totalPermits = [
            'TP' => TemporaryPermit::whereYear('created_at', $year)->whereMonth('created_at', $month)->count(),
            'MP' => MonthlyPermit::whereYear('created_at', $year)->whereMonth('created_at', $month)->count(),
            'VH' => VehiclePermit::whereYear('created_at', $year)->whereMonth('created_at', $month)->count(),
        ];

        $totalPermitsAll = array_sum($totalPermits);

        // --- Total Monthly Revenue (from payments table - source of truth) ---
        $totalRevenue = Payment::where('status', 'Paid')
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->sum('amount_total');

        // --- Permits by Company (combining all three tables) ---
        $tempCompanies = TemporaryPermit::select('company_name', \DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->groupBy('company_name')->get();
        
        $monthlyCompanies = MonthlyPermit::select('company_name', \DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->groupBy('company_name')->get();
        
        $vehicleCompanies = VehiclePermit::select('company_name', \DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->groupBy('company_name')->get();
        
        // Merge and sum by company name
        $companiesData = collect();
        foreach ([$tempCompanies, $monthlyCompanies, $vehicleCompanies] as $collection) {
            foreach ($collection as $item) {
                $existing = $companiesData->firstWhere('company_name', $item->company_name);
                if ($existing) {
                    $existing->total += $item->total;
                } else {
                    $companiesData->push((object)['company_name' => $item->company_name, 'total' => $item->total]);
                }
            }
        }
        $companiesData = $companiesData->sortByDesc('total')->values();

        $companies = $companiesData->pluck('company_name')->toArray();
        $permitCounts = $companiesData->pluck('total')->toArray();

        // --- Permit Type Revenue (from payments table - source of truth) ---
        $permitRevenue = [
            Payment::where('status', 'Paid')->where('permit_type', 'TP')->whereYear('payment_date', $year)->whereMonth('payment_date', $month)->sum('amount_total'),
            Payment::where('status', 'Paid')->where('permit_type', 'MP')->whereYear('payment_date', $year)->whereMonth('payment_date', $month)->sum('amount_total'),
            Payment::where('status', 'Paid')->where('permit_type', 'VH')->whereYear('payment_date', $year)->whereMonth('payment_date', $month)->sum('amount_total')
        ];

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
            'permitTypes' => ['TP', 'MP', 'VH'],
            'permitRevenue' => $permitRevenue,
            'months' => $months,
            'selectedMonth' => (int)$month
        ]);
    }

    public function getMonthData(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = date('Y');

        // Total Monthly Revenue (from payments table - source of truth)
        $totalRevenue = Payment::where('status', 'Paid')
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->sum('amount_total');

        // Total Permits by Type
        $totalPermits = [
            'TP' => TemporaryPermit::whereYear('created_at', $year)->whereMonth('created_at', $month)->count(),
            'MP' => MonthlyPermit::whereYear('created_at', $year)->whereMonth('created_at', $month)->count(),
            'VH' => VehiclePermit::whereYear('created_at', $year)->whereMonth('created_at', $month)->count(),
        ];

        // Permits by Company (combining all three tables)
        $tempCompanies = TemporaryPermit::select('company_name', \DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->groupBy('company_name')->get();
        
        $monthlyCompanies = MonthlyPermit::select('company_name', \DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->groupBy('company_name')->get();
        
        $vehicleCompanies = VehiclePermit::select('company_name', \DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $year)->whereMonth('created_at', $month)
            ->groupBy('company_name')->get();
        
        // Merge and sum by company name
        $companiesData = collect();
        foreach ([$tempCompanies, $monthlyCompanies, $vehicleCompanies] as $collection) {
            foreach ($collection as $item) {
                $existing = $companiesData->firstWhere('company_name', $item->company_name);
                if ($existing) {
                    $existing->total += $item->total;
                } else {
                    $companiesData->push((object)['company_name' => $item->company_name, 'total' => $item->total]);
                }
            }
        }
        $companiesData = $companiesData->sortByDesc('total')->values();

        $companies = $companiesData->pluck('company_name');
        $permitCounts = $companiesData->pluck('total');

        // Permit Type Revenue (from payments table - source of truth)
        $permitRevenue = [
            Payment::where('status', 'Paid')->where('permit_type', 'TP')->whereYear('payment_date', $year)->whereMonth('payment_date', $month)->sum('amount_total'),
            Payment::where('status', 'Paid')->where('permit_type', 'MP')->whereYear('payment_date', $year)->whereMonth('payment_date', $month)->sum('amount_total'),
            Payment::where('status', 'Paid')->where('permit_type', 'VH')->whereYear('payment_date', $year)->whereMonth('payment_date', $month)->sum('amount_total')
        ];

        // Today's revenue (from payments table - source of truth, matching currently selected month/year filter and today)
        $todayRevenue = Payment::where('status', 'Paid')
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->whereDate('payment_date', Carbon::today())
            ->sum('amount_total');

        // Daily Permits (always today, regardless of month filter)
        $dailyPermits = [
            'TP' => TemporaryPermit::whereDate('created_at', Carbon::today())->count(),
            'MP' => MonthlyPermit::whereDate('created_at', Carbon::today())->count(),
            'VH' => VehiclePermit::whereDate('created_at', Carbon::today())->count(),
        ];

        return response()->json([
            'totalRevenue' => $totalRevenue,
            'totalPermits' => $totalPermits,
            'totalPermitsAll' => array_sum($totalPermits),
            'dailyPermits' => $dailyPermits,
            'dailyPermitsAll' => array_sum($dailyPermits),
            'companies' => $companies,
            'permitCounts' => $permitCounts,
            'permitRevenue' => $permitRevenue,
            'dailyRevenue' => $todayRevenue
        ]);
    }
}
