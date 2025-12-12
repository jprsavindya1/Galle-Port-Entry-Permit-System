<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Security Gate - Permit Verification</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #002B5C 0%, #004080 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .security-header {
            background: #002B5C;
            border-bottom: 4px solid #FFC107;
            padding: 1rem 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-container img {
            height: 60px;
            width: 60px;
        }
        
        .brand-title {
            color: #FFC107;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }
        
        .security-badge {
            background: #FFC107;
            color: #002B5C;
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 1rem auto;
            padding: 0 1rem;
            max-height: calc(100vh - 150px);
            overflow-y: auto;
        }
        
        .search-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            margin-bottom: 1rem;
        }
        
        .search-title {
            color: #002B5C;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .search-input-group {
            position: relative;
            margin-bottom: 0.75rem;
        }
        
        .search-input {
            font-size: 1.2rem;
            padding: 0.75rem 1rem;
            border: 3px solid #002B5C;
            border-radius: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .search-input:focus {
            border-color: #FFC107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        
        .search-btn {
            font-size: 1.1rem;
            padding: 0.75rem 2rem;
            background: #002B5C;
            border: none;
            border-radius: 0.75rem;
            font-weight: 700;
            transition: all 0.3s;
        }
        
        .search-btn:hover {
            background: #004080;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        
        .clear-btn {
            font-size: 1rem;
            padding: 0.6rem 1.5rem;
            background: #6c757d;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
        }
        
        .result-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            display: none;
        }
        
        .result-card.show {
            display: block;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .status-badge {
            font-size: 1.2rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 700;
            text-align: center;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .status-valid {
            background: #d4edda;
            color: #155724;
            border: 3px solid #28a745;
        }
        
        .status-invalid {
            background: #f8d7da;
            color: #721c24;
            border: 3px solid #dc3545;
        }
        
        .status-cancelled {
            background: #fff3cd;
            color: #856404;
            border: 3px solid #ffc107;
        }
        
        .info-row {
            padding: 0.6rem 0.75rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 700;
            color: #002B5C;
            font-size: 0.95rem;
            min-width: 180px;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .info-value {
            font-size: 1rem;
            color: #333;
            font-weight: 600;
        }
        
        .alert-message {
            font-size: 1rem;
            padding: 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            display: none;
            text-align: center;
        }
        
        .alert-message.show {
            display: block;
            animation: slideIn 0.3s ease-out;
        }
        
        .logout-btn {
            background: #FFC107;
            color: #002B5C;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .logout-btn:hover {
            background: #ffca2c;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .search-card {
                padding: 1.5rem;
            }
            
            .search-input {
                font-size: 1.2rem;
                padding: 0.75rem 1rem;
            }
            
            .search-btn {
                font-size: 1.1rem;
                padding: 0.75rem 1.5rem;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .info-label {
                min-width: auto;
                margin-bottom: 0.5rem;
            }
        }
        
        .spinner-border-lg {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="security-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo-container">
                    <img src="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}" alt="SLPA Logo">
                    <div>
                        <h1 class="brand-title">SLPA Gate Security (Galle)</h1>
                        <span class="security-badge">PERMIT VERIFICATION</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-white">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn logout-btn">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Search Card -->
        <div class="search-card">
            <h2 class="search-title">
                <i class="bi bi-search text-primary"></i>
                Search Permit by ID
            </h2>
            
            <form id="searchForm">
                <div class="search-input-group">
                    <input 
                        type="text" 
                        id="permitId" 
                        class="form-control search-input" 
                        placeholder="Enter Permit ID" 
                        autocomplete="off"
                        autofocus>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button type="submit" class="btn btn-primary search-btn">
                        <i class="bi bi-search"></i> Search Permit
                    </button>
                    <button type="button" class="btn btn-secondary clear-btn" id="clearBtn">
                        <i class="bi bi-x-circle"></i> Clear
                    </button>
                </div>
            </form>
        </div>

        <!-- Alert Message -->
        <div id="alertMessage" class="alert alert-message"></div>

        <!-- Result Card -->
        <div id="resultCard" class="result-card">
            <!-- Status Badge -->
            <div id="statusBadge" class="status-badge"></div>
            
            <!-- Permit Details -->
            <div id="permitDetails">
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-card-text"></i> Permit ID
                    </div>
                    <div class="info-value" id="displayPermitId"></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-person-fill"></i> Full Name
                    </div>
                    <div class="info-value" id="displayFullName"></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-credit-card-2-front"></i> ID Type
                    </div>
                    <div class="info-value" id="displayIdType"></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-123"></i> ID Number
                    </div>
                    <div class="info-value" id="displayIdNumber"></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-calendar-check"></i> Valid From
                    </div>
                    <div class="info-value" id="displayFromDate"></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-calendar-x"></i> Valid Until
                    </div>
                    <div class="info-value" id="displayToDate"></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-car-front"></i> Vehicle Number
                    </div>
                    <div class="info-value" id="displayVehicleNumber"></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-truck"></i> Vehicle Type
                    </div>
                    <div class="info-value" id="displayVehicleType"></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-building"></i> Company Name
                    </div>
                    <div class="info-value" id="displayCompanyName"></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="bi bi-tag"></i> Permit Type
                    </div>
                    <div class="info-value" id="displayPermitType"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('searchForm');
            const permitIdInput = document.getElementById('permitId');
            const clearBtn = document.getElementById('clearBtn');
            const resultCard = document.getElementById('resultCard');
            const alertMessage = document.getElementById('alertMessage');
            const statusBadge = document.getElementById('statusBadge');

            // Search form submission
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const permitId = permitIdInput.value.trim().toUpperCase();
                
                if (!permitId) {
                    showAlert('Please enter a Permit ID', 'warning');
                    return;
                }

                // Show loading
                statusBadge.innerHTML = '<span class="spinner-border spinner-border-lg" role="status"></span>';
                statusBadge.className = 'status-badge';
                resultCard.classList.add('show');
                hideAlert();

                // Make AJAX request
                fetch('{{ route("security.search") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ permit_id: permitId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayPermit(data.permit);
                    } else {
                        resultCard.classList.remove('show');
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(error => {
                    resultCard.classList.remove('show');
                    showAlert('Error searching permit. Please try again.', 'danger');
                    console.error('Error:', error);
                });
            });

            // Clear button
            clearBtn.addEventListener('click', function() {
                permitIdInput.value = '';
                resultCard.classList.remove('show');
                hideAlert();
                permitIdInput.focus();
            });

            // Display permit information
            function displayPermit(permit) {
                // Set status badge
                let statusClass = 'status-invalid';
                let statusIcon = 'bi-x-circle-fill';
                
                if (permit.is_valid) {
                    statusClass = 'status-valid';
                    statusIcon = 'bi-check-circle-fill';
                } else if (permit.status === 'Cancelled') {
                    statusClass = 'status-cancelled';
                    statusIcon = 'bi-exclamation-triangle-fill';
                }
                
                statusBadge.className = `status-badge ${statusClass}`;
                statusBadge.innerHTML = `<i class="bi ${statusIcon}"></i> ${permit.validity_message}`;

                // Fill in details
                document.getElementById('displayPermitId').textContent = permit.permit_id || 'N/A';
                document.getElementById('displayFullName').textContent = permit.full_name || 'N/A';
                document.getElementById('displayIdType').textContent = permit.id_type || 'N/A';
                document.getElementById('displayIdNumber').textContent = permit.id_number || 'N/A';
                document.getElementById('displayFromDate').textContent = permit.from_date || 'N/A';
                document.getElementById('displayToDate').textContent = permit.to_date || 'N/A';
                document.getElementById('displayVehicleNumber').textContent = permit.vehicle_number || 'N/A';
                document.getElementById('displayVehicleType').textContent = permit.vehicle_type || 'N/A';
                document.getElementById('displayCompanyName').textContent = permit.company_name || 'N/A';
                document.getElementById('displayPermitType').textContent = permit.type || 'N/A';

                resultCard.classList.add('show');
            }

            // Show alert message
            function showAlert(message, type) {
                alertMessage.className = `alert alert-${type} alert-message show`;
                alertMessage.textContent = message;
            }

            // Hide alert message
            function hideAlert() {
                alertMessage.classList.remove('show');
            }

            // Allow Enter key to search
            permitIdInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchForm.dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>
</body>
</html>
