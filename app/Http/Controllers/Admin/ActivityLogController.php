<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of system activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        // Search by user_name, action, or ip_address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%$search%")
                  ->orWhere('action', 'like', "%$search%")
                  ->orWhere('ip_address', 'like', "%$search%")
                  ->orWhere('role', 'like', "%$search%");
            });
        }

        // Filter by role
        if ($request->filled('role_filter')) {
            $query->where('role', $request->role_filter);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        $roles = ActivityLog::distinct()->whereNotNull('role')->pluck('role');

        return view('admin.activity_logs.index', compact('logs', 'roles'));
    }
}
