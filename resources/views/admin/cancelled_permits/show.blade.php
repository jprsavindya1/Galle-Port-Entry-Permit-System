@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .permit-details-card {
        background: linear-gradient(135deg, #ffebee 0%, #fff5f5 100%);
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px rgba(229, 57, 53, 0.15);
        padding: 2rem;
        border: 2px solid #ffcdd2;
    }
    
    .permit-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #ef5350;
    }
    
    .permit-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #c62828;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .permit-status-badge {
        background: #e53935;
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(229, 57, 53, 0.3);
    }
    
    .details-section {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .detail-row {
        display: flex;
        padding: 1rem;
        border-bottom: 1px solid #f5f5f5;
        transition: background 0.2s ease;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-row:hover {
        background: #fafafa;
    }
    
    .detail-label {
        flex: 0 0 35%;
        font-weight: 600;
        color: #d32f2f;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
    }
    
    .detail-label i {
        color: #ef5350;
        font-size: 1.1rem;
    }
    
    .detail-value {
        flex: 1;
        color: #424242;
        font-size: 0.95rem;
        word-break: break-word;
        padding-left: 1rem;
    }
    
    .detail-value strong {
        color: #212121;
    }
    
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #ffcdd2;
    }
    
    .btn-back {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border: none;
        color: white;
        padding: 0.65rem 1.5rem;
        border-radius: 0.6rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 3px 10px rgba(108, 117, 125, 0.3);
    }
    
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
        color: white;
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #c62828;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #ffebee;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-icon {
        width: 36px;
        height: 36px;
        background: #ffebee;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e53935;
    }
</style>

<div class="container py-4">
    <div class="permit-details-card mx-auto" style="max-width:1000px;">
        
        <!-- Header Section -->
        <div class="permit-header">
            <div class="permit-title">
                <div class="info-icon">
                    <i class="bi bi-file-earmark-x-fill"></i>
                </div>
                <span>Cancelled Permit Details</span>
            </div>
            <div class="permit-status-badge">
                <i class="bi bi-x-circle-fill me-1"></i> CANCELLED
            </div>
        </div>

        <!-- Details Section -->
        <div class="details-section">
            @php
                $attributes = $cancelledPermit->getAttributes();
                $fieldIcons = [
                    'id' => 'bi-hash',
                    'permit_id' => 'bi-file-text',
                    'submission_id' => 'bi-card-text',
                    'cancelled_date' => 'bi-calendar-x',
                    'cancelled_time' => 'bi-clock',
                    'cancelled_by' => 'bi-person-x',
                    'reason' => 'bi-chat-left-text',
                    'remarks' => 'bi-sticky',
                    'created_at' => 'bi-calendar-plus',
                    'updated_at' => 'bi-calendar-check',
                ];
                
                $fieldLabels = [
                    'id' => 'Record ID',
                    'permit_id' => 'Permit ID',
                    'submission_id' => 'Submission ID',
                    'cancelled_date' => 'Cancellation Date',
                    'cancelled_time' => 'Cancellation Time',
                    'cancelled_by' => 'Cancelled By',
                    'reason' => 'Cancellation Reason',
                    'remarks' => 'Additional Remarks',
                    'created_at' => 'Record Created',
                    'updated_at' => 'Last Updated',
                ];
            @endphp

            @foreach($attributes as $key => $value)
                <div class="detail-row">
                    <div class="detail-label">
                        <i class="bi {{ $fieldIcons[$key] ?? 'bi-info-circle' }}"></i>
                        <span>{{ $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</span>
                    </div>
                    <div class="detail-value">
                        @if($key === 'cancelled_date' && $value)
                            <strong>{{ \Carbon\Carbon::parse($value)->format('F d, Y') }}</strong>
                        @elseif($key === 'cancelled_time' && $value)
                            <strong>{{ \Carbon\Carbon::parse($value)->format('h:i A') }}</strong>
                        @elseif(in_array($key, ['created_at', 'updated_at']) && $value)
                            {{ \Carbon\Carbon::parse($value)->format('F d, Y h:i A') }}
                        @elseif($key === 'reason' && $value)
                            <strong style="color: #d32f2f;">{{ $value }}</strong>
                        @elseif(empty($value))
                            <span style="color: #9e9e9e; font-style: italic;">Not provided</span>
                        @else
                            {{ $value }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.cancelled_permits.index') }}" class="btn btn-back">
                <i class="bi bi-arrow-left-circle me-2"></i> Back to List
            </a>
        </div>
    </div>
</div>

@endsection
