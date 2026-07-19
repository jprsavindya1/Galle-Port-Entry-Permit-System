@extends('layouts.app')

@section('title', 'Database Backup Management')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .user-dashboard-card { 
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%); 
        border-radius: 1rem; 
        box-shadow: 0 3px 15px rgba(0,0,0,0.08); 
        padding: 2rem; 
        margin-bottom: 2rem; 
        border: none; 
    }
    .user-dashboard-title { 
        font-size: 1.75rem; 
        font-weight: 600; 
        color: #1976d2; 
    }
    .summary-card {
        padding: 1.5rem;
        text-align: center;
        border-radius: 0.75rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.05);
        background-color: #fff;
        border: 1px solid #bbdefb;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    .summary-card h5 { 
        font-size: 0.95rem; 
        color: #1976d2; 
        font-weight: 600; 
        margin-bottom: 0.75rem; 
    }
    .summary-card h3 { 
        font-size: 2.2rem; 
        font-weight: 700; 
        color: #333; 
        margin-bottom: 0.5rem; 
    }
    .user-dashboard-table { 
        background: #f5faff; 
        border-radius: 0.75rem; 
        overflow: hidden; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.04); 
    }
    .user-dashboard-table th { 
        background: #e3f2fd; 
        color: #1976d2; 
        font-weight: 600; 
        border-bottom: 2px solid #bbdefb; 
        padding: 0.75rem 1rem; 
    }
    .user-dashboard-table td { 
        background: #f8fafc; 
        color: #333; 
        vertical-align: middle; 
        padding: 0.75rem 1rem; 
    }
    .user-dashboard-table tr:hover td {
        background-color: #eef7ff;
    }
    .badge-db {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-db-name {
        background-color: #fff;
        color: #1976d2;
        border: 1px solid #bbdefb;
    }
    .badge-db-driver {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }
    .terminal-mockup {
        background-color: #0f172a;
        color: #38bdf8;
        border-radius: 0.5rem;
        border: 1px solid #334155;
        padding: 1rem;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }
</style>

<div class="container py-4">
    <!-- Main Card wrapper -->
    <div class="user-dashboard-card">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-3 border-bottom" style="border-color: #bbdefb !important;">
            <div>
                <div class="user-dashboard-title d-flex align-items-center gap-2">
                    <i class="bi bi-cloud-arrow-down-fill"></i>
                    <span>Database Backup Management</span>
                </div>
                <p class="text-muted mb-0 mt-1" style="font-size: 0.9rem;">Generate, download, and manage system database SQL backups.</p>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <span class="badge-db badge-db-name">
                    <i class="bi bi-database"></i> DB: {{ $databaseName }}
                </span>
                <span class="badge-db badge-db-driver">
                    <i class="bi bi-check-circle-fill"></i> MySQL
                </span>
            </div>
        </div>

        <!-- Summary Cards Grid -->
        <div class="row mb-4 g-4">
            <div class="col-md-4">
                <div class="summary-card">
                    <h5>TOTAL TABLES</h5>
                    <h3>{{ $tableCount }}</h3>
                    <div class="text-muted" style="font-size: 0.85rem;">
                        <i class="bi bi-grid-3x3-gap-fill text-primary"></i> Tables Schema
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="summary-card">
                    <h5>LIVE RECORDS</h5>
                    <h3>{{ number_format($totalRows) }}</h3>
                    <div class="text-muted" style="font-size: 0.85rem;">
                        <i class="bi bi-database-fill-down text-success"></i> Rows Count
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="summary-card">
                    <h5>STORAGE SIZE</h5>
                    <h3>{{ $totalSize >= 1024 ? number_format($totalSize / 1024, 2) . ' MB' : $totalSize . ' KB' }}</h3>
                    <div class="text-muted" style="font-size: 0.85rem;">
                        <i class="bi bi-hdd-network-fill text-warning"></i> Data & Index Size
                    </div>
                </div>
            </div>
        </div>

        <!-- Download Action Card -->
        <div class="text-center py-4 px-3 mb-4 rounded-3" style="background-color: #f5faff; border: 1px solid #bbdefb;">
            <i class="bi bi-shield-check text-success fs-1 mb-2 d-block"></i>
            <h4 class="font-semibold text-dark mb-2">Create & Download SQL Backup</h4>
            <p class="text-muted mx-auto mb-3" style="max-width: 550px; font-size: 0.9rem;">
                Clicking the button below generates a complete SQL dump featuring table structures (schemas) and active record contents.
            </p>
            <a href="{{ route('admin.backup.download') }}" class="btn btn-primary btn-lg px-4" style="border-radius: 0.5rem; background-color: #1976d2; border-color: #1976d2;">
                <i class="bi bi-cloud-arrow-down-fill me-2"></i> Generate & Download SQL Backup
            </a>
        </div>

        <!-- Restore Instructions -->
        <div class="p-3 mb-4 rounded-3" style="background-color: #f8fafc; border: 1px solid #dee2e6;">
            <h5 class="mb-3 text-dark font-semibold">
                <i class="bi bi-arrow-counterclockwise text-primary me-2"></i> Database Restore & Recovery Instructions
            </h5>
            
            <div class="mb-3">
                <strong>1. Using phpMyAdmin:</strong>
                <p class="text-muted mb-0 ms-3" style="font-size: 0.88rem;">
                    Open phpMyAdmin, select your database, click the <strong>Import</strong> tab, browse and choose the downloaded SQL file, and click <strong>Go / Import</strong>.
                </p>
            </div>

            <div>
                <strong>2. Using MySQL Command Line:</strong>
                <p class="text-muted mb-0 ms-3" style="font-size: 0.88rem;">
                    Execute the command below inside your terminal or PowerShell console:
                </p>
                <div class="terminal-mockup ms-3">
                    mysql -u [username] -p {{ $databaseName }} &lt; {{ $databaseName }}_backup_filename.sql
                </div>
            </div>
        </div>

        <!-- Tables Overview -->
        <div>
            <h5 class="mb-3 text-dark font-semibold">
                <i class="bi bi-table text-primary me-2"></i> Database Tables Schema Overview
            </h5>
            <div class="table-responsive">
                <table class="table user-dashboard-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 10%;">#</th>
                            <th>Table Name</th>
                            <th class="text-end" style="width: 25%;">Rows Count</th>
                            <th class="text-end" style="width: 25%;">Data Size</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tables as $index => $table)
                            <tr>
                                <td class="text-muted font-semibold">{{ $index + 1 }}</td>
                                <td class="font-semibold" style="color: #0f172a;">{{ $table->table }}</td>
                                <td class="text-end font-semibold text-dark">{{ number_format($table->rows) }}</td>
                                <td class="text-end text-muted font-semibold">
                                    {{ $table->size >= 1024 ? number_format($table->size / 1024, 2) . ' MB' : $table->size . ' KB' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
