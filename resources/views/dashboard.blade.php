@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
.dashboard-card {
  position: relative;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  overflow: hidden;
}

.dashboard-card::after {
  content: "";
  position: absolute;
  right: 0;
  top: 0;
  width: 0;
  height: 100%;
  background: linear-gradient(180deg, #0073e6, #4fc3f7);
  transition: width 0.3s ease;
  z-index: 1;
}

.dashboard-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.dashboard-card:hover::after {
  width: 5px;
}

    .summary-card {
        padding: 1.5rem;
        text-align: center;
        border-radius: 0.75rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    }
    .summary-card h3 { font-size: 2rem; margin-bottom: 0.5rem; }
    .summary-card h5 { font-size: 1rem; color: #555; margin-bottom: 1rem; }
    .summary-breakdown {
        display: flex;
        justify-content: space-around;
        margin-top: 1rem;
        font-weight: 500;
        color: #333;
    }
    .summary-breakdown div {
        flex: 1;
        text-align: center;
        padding: 0.5rem;
        background: #caddfaff;
        border-radius: 0.5rem;
        margin: 0 0.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    canvas { max-height: 220px; }

    .icon-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-bottom: 0.75rem;
}

.card-icon {
    width: 50px;
    height: 50px;
    opacity: 0.7;
    transition: transform 0.3s ease, opacity 0.3s ease;
    filter: grayscale(100%);
}

/* On hover — make GIF play  */
.dashboard-card:hover .card-icon {
    opacity: 1;
    transform: scale(1.15);
    filter: grayscale(0%);
}

</style>

<div class="container">
    <div class="row align-items-center mb-4 pb-3" style="border-bottom: 3px solid #002B5C;">
        <div class="col-md-6">
            <h2 class="mb-0" style="font-weight: 700; color: #002B5C;">Dashboard</h2>
        </div>
        @auth
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-md-end gap-3">
                    <div class="text-end">
                        <div style="font-size: 0.7rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Current User</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #002B5C;">{{ Auth::user()->name }}</div>
                    </div>
                    <div class="vr" style="height: 40px; opacity: 0.3;"></div>
                    <div class="px-4 py-2 rounded" style="background-color: #002B5C; border-left: 4px solid #FFC107;">
                        <div style="font-size: 0.65rem; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1px; font-weight: 600; margin-bottom: 2px;">Role</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: #FFC107; text-transform: uppercase; letter-spacing: 0.5px;">{{ Auth::user()->role }}</div>
                    </div>
                </div>
            </div>
        @endauth
    </div>

   <!-- --- Summary Cards Row --- -->
<div class="row mb-3">
    <!-- Daily Permits Card -->
    <div class="col-md-4 mb-2">
        <div class="summary-card h-100">
            <h5>Daily Permits ({{ now()->format('Y-m-d') }})</h5>
            <h3>{{ ($dailyPermits['TP'] ?? 0) + ($dailyPermits['MP'] ?? 0) + ($dailyPermits['VH'] ?? 0) }}</h3>
            <div class="summary-breakdown">
                <div>TP<br>{{ $dailyPermits['TP'] ?? 0 }}</div>
                <div>MP<br>{{ $dailyPermits['MP'] ?? 0 }}</div>
                <div>VH<br>{{ $dailyPermits['VH'] ?? 0 }}</div>
            </div>
        </div>
    </div>



    <!-- Daily Revenue Card -->
    <div class="col-md-4 mb-2">
        <div class="summary-card h-100">
            <h5>Daily Revenue ({{ now()->format('Y-m-d') }})</h5>
            <h3 id="dailyRevenue">LKR {{ number_format($dailyRevenue ?? 0, 2) }}</h3>
        </div>
    </div>

        <!-- Total Monthly Revenue Card -->
    <div class="col-md-4 mb-2">
        <div class="summary-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Total Monthly Revenue</h5>
                <select id="monthFilterSelect" class="form-select form-select-sm w-auto">
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <h3>LKR {{ number_format($totalRevenue ?? 0, 2) }}</h3>
        </div>
    </div>
</div>


    <!-- --- Charts Row --- -->
    <div class="row mb-4">
        <div class="col-md-6 mb-2">
            <div class="card shadow-sm rounded-3 p-2">
                <h5 class="mb-2">Permits by Company</h5>
                <canvas id="companyBarChart"></canvas>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="card shadow-sm rounded-3 p-2">
                <h5 class="mb-2">Permit Revenue Insights</h5>
                <canvas id="permitPieChart"></canvas>
            </div>
        </div>
    </div>

    <!-- --- Action Cards Row --- -->
    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('permit.temporary') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="icon-wrapper">
                        <img src="{{ asset('images/notes.gif') }}" class="card-icon" alt="Icon">
                    </div>
                    <h4>Temporary Permit</h4>
                    <p>Create a new temporary permit request.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('permit.monthly') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="icon-wrapper">
                        <img src="{{ asset('images/notepad.gif') }}" class="card-icon" alt="Icon">
                    </div>
                    <h4>Monthly Permit</h4>
                    <p>Create a new monthly permit request.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('permit.vehicle') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="icon-wrapper">
                        <img src="{{ asset('images/file.gif') }}" class="card-icon" alt="Icon">
                    </div>
                    <h4>Vehicle Permit</h4>
                    <p>Create a new vehicle permit request.</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('permits.submitted') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <div class="icon-wrapper">
                        <img src="{{ asset('images/checklist.gif') }}" class="card-icon" alt="Icon">
                    </div>
                    <h4>View all permit requests</h4>
                    <p>Permit List</p>
                </div>
            </a>
        </div>

        @auth
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'super-admin')
            <div class="col-md-4">
                <a href="{{ route('blacklist.index') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <div class="icon-wrapper">
                        <img src="{{ asset('images/cyberterrorism.gif') }}" class="card-icon" alt="Icon">
                    </div>
                        <h4>Edit BlackList</h4>
                        <p>BlackList</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('admin.payment_settings.edit') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <div class="icon-wrapper">
                        <img src="{{ asset('images/payment.gif') }}" class="card-icon" alt="Icon">
                    </div>
                        <h4>Edit Payment Information</h4>
                        <p>Configure rates, taxes and pass pricing</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('users.index') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <div class="icon-wrapper">
                        <img src="{{ asset('images/user.gif') }}" class="card-icon" alt="Icon">
                    </div>
                        <h4>Manage Users</h4>
                        <p>Create, edit, and delete system users</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('admin.cancelled_permits.index') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <div class="icon-wrapper">
                        <img src="{{ asset('images/no-data.gif') }}" class="card-icon" alt="Icon">
                    </div>
                        <h4>Cancelled Permits</h4>
                        <p>View and manage cancelled permit requests.</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('admin.masterdata') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <div class="icon-wrapper">
                        <img src="{{ asset('images/settings.gif') }}" class="card-icon" alt="Icon">
                    </div>
                        <h4>Edit Master Data</h4>
                        <p>companies, designations, vehicles, reasons</p>
                    </div>
                </a>
            </div>
        @endif
        @endauth
    </div>
</div>

<script>
    // Initial Data from Controller
    const companies = @json($companies ?? []);
    const permitCounts = @json($permitCounts ?? []);
    const permitTypes = ['TP','MP','VH'];
    const permitRevenue = @json($permitRevenue ?? [0,0,0]);

    let companyChart, permitChart;

    // --- Chart Rendering Function ---
    function renderCharts(companies, counts, types, revenues) {
        // Destroy old charts
        if (companyChart) companyChart.destroy();
        if (permitChart) permitChart.destroy();

        // --- Bar Chart (Permits by Company) ---
        const ctxBar = document.getElementById('companyBarChart').getContext('2d');
        const gradient = ctxBar.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, '#6a9ed5ff');
        gradient.addColorStop(1, '#1e3c72ff');

        companyChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: companies,
                datasets: [{
                    label: 'Number of Permits',
                    data: counts,
                    backgroundColor: gradient,
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // --- Pie Chart (Revenue by Type) ---
        const ctxPie = document.getElementById('permitPieChart').getContext('2d');
        permitChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: types,
                datasets: [{
                    data: revenues,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.label + ': LKR ' + ctx.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // --- Initial Render ---
    renderCharts(companies, permitCounts, permitTypes, permitRevenue);

    // --- Month Filter Change Handler ---
    $('#monthFilterSelect').on('change', function() {
    const month = $(this).val();
    $.get('{{ route("dashboard.data") }}', { month: month }, function(res) {

        // Update Total Cards
        const cards = $('.summary-card h3');
        cards.eq(0).text(res.dailyPermitsAll); // Daily Permits
        cards.eq(1).text('LKR ' + Number(res.dailyRevenue).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })); // Daily Revenue
        cards.eq(2).text('LKR ' + Number(res.totalRevenue).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })); // Monthly Revenue

        // Update Breakdown
        const breakdown = $('.summary-breakdown div');
        breakdown.eq(0).html('TP<br>' + res.dailyPermits.TP);
        breakdown.eq(1).html('MP<br>' + res.dailyPermits.MP);
        breakdown.eq(2).html('VH<br>' + res.dailyPermits.VH);

        // Update Charts
        renderCharts(res.companies, res.permitCounts, ['TP','MP','VH'], res.permitRevenue);
    });
});

</script>
@endsection
