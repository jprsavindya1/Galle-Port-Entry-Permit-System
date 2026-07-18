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
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="mb-1 text-[#13314C]" style="font-weight: 700; font-size: 1.75rem;">Welcome back, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem; font-weight: 500;">Here's what's happening with your permits today.</p>
        </div>
        <div class="d-flex align-items-center bg-white border border-gray-200 rounded-3 px-3 py-2 shadow-sm text-[#13314C]" style="font-weight: 600; font-size: 0.9rem; gap: 8px;">
            <i class="bi bi-calendar3 text-[#0b5ed7]"></i>
            <span>{{ now()->format('d F Y') }}</span>
        </div>
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
        <!-- Permits by Company (Bar Chart) -->
        <div class="col-md-6 mb-2">
            <div class="card shadow-sm rounded-3 p-3 h-100 border-0 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;">
                <h5 class="mb-3 text-[#13314C] font-semibold" style="font-size: 1.1rem; font-family: 'Outfit', sans-serif;">Permits by Company</h5>
                <div style="height: 220px; position: relative;">
                    <canvas id="companyBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Permit Revenue Insights (Doughnut Chart) -->
        <div class="col-md-6 mb-2">
            <div class="card shadow-sm rounded-3 p-3 h-100 border-0 bg-white" style="box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;">
                <h5 class="mb-3 text-[#13314C] font-semibold" style="font-size: 1.1rem; font-family: 'Outfit', sans-serif;">Permit Revenue Insights</h5>
                <div class="d-flex align-items-center justify-content-between gap-2" style="height: 220px; width: 100%;">
                    <!-- Doughnut Canvas -->
                    <div class="position-relative" style="width: 200px; height: 200px; flex-shrink: 0;">
                        <canvas id="permitPieChart" style="position: relative; z-index: 5; background: transparent;"></canvas>
                        <!-- Centered circle container -->
                        <div class="position-absolute bg-white rounded-circle shadow-sm d-flex flex-column align-items-center justify-content-center" 
                             style="width: 138px; height: 138px; top: 50%; left: 50%; transform: translate(-50%, -50%); border: 1px solid rgba(0,0,0,0.03); pointer-events: none; z-index: 2; box-shadow: 0 8px 24px rgba(19, 49, 76, 0.06) !important;">
                            <span class="text-uppercase tracking-wider font-semibold text-center text-muted" style="font-size: 0.55rem; line-height: 1.25; font-family: 'Outfit', sans-serif;">TOTAL REVENUE<br><span style="font-size: 0.48rem; opacity: 0.8;">COLLECTION TOTAL</span></span>
                            <span id="doughnutCenterText" class="text-[#13314C]" style="font-weight: 700; font-size: 1.15rem; font-family: 'Outfit', sans-serif; margin-top: 4px; line-height: 1;">LKR {{ number_format($totalRevenue ?? 0, 0) }}</span>
                        </div>
                    </div>
                    
                    <!-- Custom Legend on the Right -->
                    <div id="customLegendContainer" class="d-flex flex-column gap-2 ps-2 flex-grow-1" style="font-family: 'Outfit', sans-serif; font-size: 0.82rem; font-weight: 600;">
                        <!-- Legends will be generated dynamically here -->
                    </div>
                </div>
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

            <div class="col-md-4">
                <a href="{{ route('admin.year_process.index') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                    <div class="card-body">
                        <div class="icon-wrapper">
                        <img src="{{ asset('images/checklist.gif') }}" class="card-icon" alt="Icon">
                    </div>
                        <h4>Year & Process</h4>
                        <p>Configure year prefix and reset cycle</p>
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

    // --- Custom inline plugin to draw bar value labels next to horizontal bars ---
    const drawValuesPlugin = {
        id: 'drawValues',
        afterDatasetsDraw(chart, args, options) {
            const { ctx, data } = chart;
            ctx.save();
            ctx.font = "bold 11px 'Outfit', sans-serif";
            ctx.fillStyle = "#13314c";
            ctx.textAlign = "left";
            ctx.textBaseline = "middle";
            
            const dataset = data.datasets[0];
            const meta = chart.getDatasetMeta(0);
            
            meta.data.forEach((bar, index) => {
                const val = Number(dataset.data[index]).toFixed(0);
                const xPos = bar.x + 8; // 8px spacing to the right of the bar
                const yPos = bar.y;
                ctx.fillText(val, xPos, yPos);
            });
            ctx.restore();
        }
    };

    // --- Chart Rendering Function ---
    function renderCharts(companies, counts, types, revenues) {
        // Destroy old charts
        if (companyChart) companyChart.destroy();
        if (permitChart) permitChart.destroy();

        // Calculate dynamic max for X axis to prevent label clipping
        const maxVal = counts.length > 0 ? Math.max(...counts) : 0;
        const xMax = maxVal > 0 ? maxVal + 1 : 4;

        // --- Bar Chart (Permits by Company) ---
        const ctxBar = document.getElementById('companyBarChart').getContext('2d');
        const gradient = ctxBar.createLinearGradient(0, 0, 320, 0); // Horizontal gradient
        gradient.addColorStop(0, '#2d56ff'); // Mockup Blue
        gradient.addColorStop(1, '#00f2fe'); // Mockup Cyan

        companyChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: companies,
                datasets: [{
                    label: 'Number of Permits',
                    data: counts,
                    backgroundColor: gradient,
                    hoverBackgroundColor: '#00f2fe',
                    borderRadius: 20, // Capsule look on both sides
                    borderSkipped: false, // Apply border radius to all corners
                    barThickness: 16
                }]
            },
            plugins: [drawValuesPlugin],
            options: {
                indexAxis: 'y', // Makes the bar chart horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#13314c',
                        titleFont: { family: "'Outfit', sans-serif", weight: 'bold' },
                        bodyFont: { family: "'Outfit', sans-serif" },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                return ' Permits: ' + Number(ctx.raw).toFixed(0);
                            }
                        }
                    }
                },
                scales: { 
                    x: {
                        max: xMax,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1,
                            color: '#94a3b8',
                            font: { family: "'Outfit', sans-serif", size: 10 }
                        }
                    },
                    y: { 
                        grid: { display: false },
                        ticks: { 
                            color: '#475569',
                            font: { family: "'Outfit', sans-serif", size: 10, weight: '500' }
                        } 
                    } 
                }
            }
        });

        // --- Doughnut Chart (Revenue by Type) ---
        const ctxPie = document.getElementById('permitPieChart').getContext('2d');
        permitChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: types,
                datasets: [{
                    data: revenues,
                    backgroundColor: ['#ff6384', '#0b5ed7', '#f6ba18'], // Premium Coral, Navy Blue, SLPA Gold
                    borderWidth: 0, // No border lines to allow perfect rounded ends
                    hoverOffset: 4,
                    borderRadius: 15, // Perfect rounded caps for slice ends
                    spacing: 4 // Space between segments
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%', // Sleek mockup cutout ring
                plugins: {
                    legend: { display: false }, // Use custom HTML legend on the right
                    tooltip: {
                        enabled: true,
                        backgroundColor: '#13314c',
                        titleFont: { family: "'Outfit', sans-serif", weight: 'bold' },
                        bodyFont: { family: "'Outfit', sans-serif" },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                const val = Number(ctx.raw);
                                const total = ctx.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                                const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                return ' ' + ctx.label + ': LKR ' + val.toLocaleString(undefined, { maximumFractionDigits: 0 }) + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });

        // --- Render Custom HTML Legend on the Right ---
        const total = revenues.reduce((a, b) => Number(a) + Number(b), 0);
        const colors = ['#ff6384', '#0b5ed7', '#f6ba18'];
        let legendHtml = '';
        
        for (let i = 0; i < types.length; i++) {
            const val = Number(revenues[i]);
            const pct = total > 0 ? Math.round((val / total) * 100) : 0;
            const color = colors[i];
            legendHtml += `
                <div class="d-flex align-items-center text-[#13314C] py-1">
                    <span class="rounded-circle me-2" style="width: 8px; height: 8px; background-color: ${color}; display: inline-block; flex-shrink: 0;"></span>
                    <span style="font-weight: 500; font-size: 0.8rem; color: #475569;">
                        <strong style="color: #1e293b; font-weight: 700;">${types[i]}</strong> - LKR ${val.toLocaleString(undefined, { maximumFractionDigits: 0 })} (${pct}%)
                    </span>
                </div>
            `;
        }
        $('#customLegendContainer').html(legendHtml);
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

            // Update Doughnut Center Total
            $('#doughnutCenterText').text('LKR ' + Number(res.totalRevenue).toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }));

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
