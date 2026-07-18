@extends('layouts.app')

@section('title', 'Year & Process Management')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .admin-card {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
    }
    .admin-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1e3a8a;
        letter-spacing: 0.5px;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #3b82f6;
        padding-bottom: 0.75rem;
    }
    .stat-box {
        background-color: #f1f5f9;
        border-radius: 0.75rem;
        padding: 1.25rem;
        border-left: 5px solid #3b82f6;
        height: 100%;
        transition: transform 0.2s ease;
    }
    .stat-box:hover {
        transform: translateY(-2px);
    }
    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
    }
    .stat-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }
    .stat-highest {
        font-size: 0.85rem;
        color: #1e3a8a;
        font-weight: 500;
    }
    .setting-label {
        font-weight: 600;
        color: #1e3a8a;
    }
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        padding: 0.6rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
    }
    .btn-primary {
        background-color: #2563eb;
        border-color: #2563eb;
        border-radius: 0.5rem;
        padding: 0.6rem 1.25rem;
        font-weight: 600;
    }
    .btn-primary:hover {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
    }
    .btn-warning {
        background-color: #d97706;
        border-color: #d97706;
        color: #fff;
        border-radius: 0.5rem;
        padding: 0.6rem 1.25rem;
        font-weight: 600;
    }
    .btn-warning:hover {
        background-color: #b45309;
        border-color: #b45309;
        color: #fff;
    }
    .info-badge {
        background-color: #eff6ff;
        color: #1e40af;
        border: 1px solid #bfdbfe;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
        font-size: 0.95rem;
    }
</style>

<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Configuration Card -->
        <div class="col-lg-7">
            <div class="admin-card">
                <div class="admin-title">
                    <i class="bi bi-gear-wide-connected me-2"></i> 
                    Year & Process Settings
                </div>

                <form action="{{ route('admin.year_process.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Active Year Input -->
                    <div class="mb-4">
                        <label for="active_year" class="form-label setting-label">
                            Active System Year
                        </label>
                        <input type="number" name="active_year" id="active_year" class="form-control" value="{{ $activeYear }}" min="2020" max="2099" required>
                        <div class="form-text text-muted">
                            This year determines the Permit ID year prefix (e.g. <code>TP26xxxx</code> for the year 2026).
                        </div>
                    </div>

                    <!-- Reset Cycle Settings -->
                    <div class="mb-4">
                        <label class="form-label setting-label d-block">
                            Permit ID Reset Frequency
                        </label>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="permit_id_reset_cycle" id="cycle_yearly" value="yearly" {{ $resetCycle === 'yearly' ? 'checked' : '' }}>
                            <label class="form-check-label" for="cycle_yearly">
                                <strong>Yearly Reset (Recommended)</strong>
                                <span class="d-block text-muted small">
                                    The sequential counter increments continuously throughout the year and only resets to <code>0001</code> at the start of a new year.
                                </span>
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="permit_id_reset_cycle" id="cycle_monthly" value="monthly" {{ $resetCycle === 'monthly' ? 'checked' : '' }}>
                            <label class="form-check-label" for="cycle_monthly">
                                <strong>Monthly Reset</strong>
                                <span class="d-block text-muted small">
                                    The sequential counter automatically resets to <code>0001</code> at the start of each month.
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Settings
                        </button>
                    </div>
                </form>

                <div class="info-badge mt-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Current Active ID Format:</strong>
                    @if($resetCycle === 'yearly')
                        <code>TP{{ substr($activeYear, -2) }}MMXXXX</code> (where <code>XXXX</code> is a continuous running counter for the entire year {{ $activeYear }}).
                    @else
                        <code>TP{{ substr($activeYear, -2) }}MMXXXX</code> (where <code>XXXX</code> resets to <code>0001</code> at the start of each month).
                    @endif
                </div>
            </div>

            <!-- Start New Year Process Card -->
            <div class="admin-card">
                <div class="admin-title">
                    <i class="bi bi-calendar-event me-2"></i>
                    Year Transition Wizard
                </div>
                <p class="text-muted">
                    When a new year begins, use this button to transition the system to the next active year and start the new sequence.
                </p>
                
                <form action="{{ route('admin.year_process.start_new_year') }}" method="POST" onsubmit="return confirm('Are you sure you want to transition the system to the next active year? This will reset the sequence counter back to 0001 under the new year.');">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-right-circle me-1"></i> Start Year {{ $activeYear + 1 }} Process
                    </button>
                </form>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="col-lg-5">
            <div class="admin-card h-100">
                <div class="admin-title">
                    <i class="bi bi-bar-chart-line me-2"></i> 
                    Yearly Statistics ({{ $activeYear }})
                </div>
                
                <p class="text-muted mb-4">
                    Permit statistics issued by the system in the active year <strong>{{ $activeYear }}</strong>:
                </p>

                <div class="row g-3">
                    <!-- Temporary Permit Stats -->
                    <div class="col-12">
                        <div class="stat-box">
                            <div class="stat-label">Temporary Permits (TP)</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="stat-number">{{ $stats['tp']['count'] }}</span>
                                <span class="stat-highest text-muted">
                                    Highest Seq: <strong>{{ $stats['tp']['highest'] }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Permit Stats -->
                    <div class="col-12">
                        <div class="stat-box" style="border-left-color: #10b981;">
                            <div class="stat-label">Monthly Permits (MP)</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="stat-number">{{ $stats['mp']['count'] }}</span>
                                <span class="stat-highest text-muted">
                                    Highest Seq: <strong>{{ $stats['mp']['highest'] }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Permit Stats -->
                    <div class="col-12">
                        <div class="stat-box" style="border-left-color: #f59e0b;">
                            <div class="stat-label">Vehicle Permits (VH)</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="stat-number">{{ $stats['vh']['count'] }}</span>
                                <span class="stat-highest text-muted">
                                    Highest Seq: <strong>{{ $stats['vh']['highest'] }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-muted small">
                    <i class="bi bi-clock-history me-1"></i>
                    Last Updated: {{ now()->format('Y-m-d H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
