# Print Status Tracking System

## Overview
This document describes the print status tracking system implemented for the Port Entry Permit application. The system tracks when permits are printed, who printed them, and displays this information in the submitted permits page.

## Features

### 1. Database Fields
Each permit table (temporary_permits, monthly_permits, vehicle_permits) now includes:
- `is_printed` (boolean): Indicates whether the permit has been printed
- `printed_at` (timestamp): When the permit was printed
- `printed_by` (foreign key): The user who printed the permit

### 2. Automatic Tracking
When a permit is printed through either:
- **Batch Print** (`/permit/print/batch/{submission_id}`)
- **Single Print** (`/permit/print/single/{permitType}/{id}`)

The system automatically:
1. Sets `is_printed` to `true`
2. Records the current timestamp in `printed_at`
3. Records the authenticated user's ID in `printed_by`

### 3. Visual Display on Submitted Page
The submitted permits page now shows:
- **Printed Status Badge**: Green badge with "Printed" for printed permits, gray badge with "Not Printed" for others
- **Print Date & Time**: Displays when the permit was printed (format: YYYY-MM-DD HH:MM)
- **Printed By**: Shows the name of the user who printed the permit

### 4. Visual Display on Invoice Page
The invoice page now includes:
- **Batch Print Status Indicator**: 
  - Shows a prominent banner indicating if all permits are printed, partially printed, or not printed
  - Color-coded: Green for all printed, Orange for partial/none
  - Displays count of printed vs total permits
- **Print Status Column**: 
  - Each permit shows individual print status
  - Displays timestamp and user who printed it
  - Compact format optimized for the invoice view
- **Dynamic Batch Print Button**: 
  - Changes label based on print status ("Batch Print Permits", "Print Remaining Permits", "Reprint All Permits")
  - Always enabled for easy access
  - Shows loading state when clicked

### 5. Print Status Column
A new "Print Status" column has been added to:
- **Submitted Permits Table**: Full detail view with timestamp and user
- **Invoice Page Table**: Compact view with essential information

Format on submitted page:
```
✓ Printed
2025-11-15 14:30
By: John Doe
```

Format on invoice page:
```
✓ Printed
Nov 15, 14:30
By: John Doe
```

Or for non-printed permits:
```
⨯ Not Printed
```

## Implementation Details

### Database Migrations
Three migration files were created:
1. `2025_11_15_100001_add_print_tracking_to_temporary_permits_table.php`
2. `2025_11_15_100002_add_print_tracking_to_monthly_permits_table.php`
3. `2025_11_15_100003_add_print_tracking_to_vehicle_permits_table.php`

### Model Updates
All three permit models (TemporaryPermit, MonthlyPermit, VehiclePermit) were updated:
- Added new fields to `$fillable` array
- Added new fields to `$casts` array
- Added `printedBy()` relationship method

### Controller Updates

#### PrintController
Updated to track print status:
- `show()` method: Tracks batch prints
- `showSingle()` method: Tracks individual prints

#### PermitController
Updated `submittedList()` method to include print tracking fields in the query results.

### View Updates

#### submitted.blade.php
- Added "Print Status" column header
- Added print status display with badges and formatting
- Shows timestamp and user who printed
- Optimized with user data pre-loading to prevent N+1 queries

#### invoice.blade.php
- Added "Print Status" column to invoice table
- Added batch print status indicator banner (shows if all/partial/none printed)
- Updated batch print button to be always enabled with dynamic labels
- Enhanced styling with color-coded status indicators
- Print status column hidden when printing invoice (print media query)
- User data pre-loaded for performance optimization

## Usage

### For Users

#### Perfect User Flow (Recommended):
1. Complete permit form and submit payment
2. Navigate to the **Invoice Page** (redirected automatically after payment)
3. Review the **Batch Print Status** banner at the top:
   - 🟢 Green banner = All permits already printed
   - 🟠 Orange banner = Some or no permits printed yet
4. Click the **"Batch Print Permits"** button to print all permits at once
5. Print status automatically updates to show all permits as printed
6. Navigate to **Submitted Permits** page to verify print status

#### Alternative Flow:
1. Navigate to the Submitted Permits page
2. Look for the "Print Status" column to see if permits have been printed
3. Click "Print" button for individual permits - the status will be automatically updated
4. Or click "View Group" to go to invoice and use batch print

### For Administrators
- Print status is visible to all authenticated users
- Only the user who printed the permit is recorded
- Print history is preserved even if permits are edited

## Benefits

1. **Accountability**: Track who printed which permits and when
2. **Audit Trail**: Complete history of print operations
3. **Efficiency**: Quickly identify which permits have been printed
4. **Transparency**: Clear visual indicators for print status on both submitted and invoice pages
5. **Batch Processing**: Streamlined workflow with batch printing from invoice page
6. **User Guidance**: Clear visual cues guide users through the optimal printing workflow
7. **Status at a Glance**: Batch status banner provides immediate overview of printing completion

## Future Enhancements

Potential improvements:
- Print count tracking (how many times a permit was printed)
- Reprint notifications for administrators
- Print history log with multiple print timestamps
- Export print reports for auditing purposes
- Filter permits by print status

## Technical Notes

- Print tracking uses database foreign keys with `ON DELETE SET NULL` to handle user deletion gracefully
- Timestamps use Laravel's datetime casting for proper timezone handling
- The system is backward compatible - existing permits show "Not Printed" status
- Print status can be filtered in future updates for reporting purposes
