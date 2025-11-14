# Separate Permit Tables - Developer Guide

## 📊 New Database Structure

### Tables Overview

| Table | Purpose | Key Fields | Type Code |
|-------|---------|------------|-----------|
| `temporary_permits` | NIC/Passport/Driving License permits (15-30 days) | `id_type`, `id_number`, `full_name`, `initials` | TP |
| `monthly_permits` | NIC-only permits (30 days) | `id_type`, `id_number`, `full_name`, `police_report_*` | MP |
| `vehicle_permits` | Vehicle entry permits | `vehicle_number`, `vehicle_type`, `owner_name` | VH |

---

## 🏗️ Model Structure

### TemporaryPermit Model
```php
use App\Models\TemporaryPermit;

// Create
TemporaryPermit::create([
    'permit_id' => 'TP25110001',
    'id_type' => 'NIC',
    'id_number' => '199012345678',
    'from_date' => '2025-11-01',
    'to_date' => '2025-11-15',
    'full_name' => 'JOHN DOE',
    'initials' => 'J.D.',
    'company_name' => 'ABC Company',
    // ... other fields
]);

// Query
$permits = TemporaryPermit::where('company_name', 'ABC Company')->get();
$active = TemporaryPermit::where('status', 'active')->get();
```

### MonthlyPermit Model
```php
use App\Models\MonthlyPermit;

// Create
MonthlyPermit::create([
    'permit_id' => 'MP25110001',
    'id_type' => 'NIC',
    'id_number' => '199012345678',
    'from_date' => '2025-11-01',
    'to_date' => '2025-11-30',
    'police_report_issue_date' => '2025-10-25',
    'police_report_expire_date' => '2026-10-25',
    // ... other fields
]);

// Query
$permits = MonthlyPermit::active()->get();
```

### VehiclePermit Model
```php
use App\Models\VehiclePermit;

// Create
VehiclePermit::create([
    'permit_id' => 'VH25110001',
    'vehicle_number' => 'ABC-1234',
    'vehicle_type' => 'Car',
    'owner_name' => 'JANE DOE',
    'from_date' => '2025-11-01',
    'to_date' => '2025-11-15',
    // ... other fields
]);

// Query
$permits = VehiclePermit::where('vehicle_number', 'ABC-1234')->get();
```

---

## 🔌 Controller Usage

### PermitController - Unified Operations

#### Querying All Permits
```php
// Get all permits (union query)
$tempPermits = TemporaryPermit::all();
$monthlyPermits = MonthlyPermit::all();
$vehiclePermits = VehiclePermit::all();

$allPermits = $tempPermits->concat($monthlyPermits)->concat($vehiclePermits);
```

#### Type-Specific Operations
```php
// Edit permit
public function edit($permitType, $id) {
    switch ($permitType) {
        case 'temporary':
            $permit = TemporaryPermit::findOrFail($id);
            break;
        case 'monthly':
            $permit = MonthlyPermit::findOrFail($id);
            break;
        case 'vehicle':
            $permit = VehiclePermit::findOrFail($id);
            break;
    }
    // ... rest of logic
}
```

---

## 🛣️ Route Usage

### Old Routes (Single Table)
```php
// ❌ OLD - No longer works
route('permits.edit', $permit->id)
route('permits.destroy', $permit->id)
```

### New Routes (Separate Tables)
```php
// ✅ NEW - Requires permit type
route('permits.edit', ['permitType' => 'temporary', 'id' => $permit->id])
route('permits.edit', ['permitType' => 'monthly', 'id' => $permit->id])
route('permits.edit', ['permitType' => 'vehicle', 'id' => $permit->id])

// Or using array syntax
route('permits.destroy', ['temporary', $permit->id])
route('permits.cancel', ['monthly', $permit->id])
```

---

## 📝 Blade View Examples

### Displaying Permits with Type
```blade
@foreach($permits as $permit)
    <tr>
        <td>{{ $permit->permit_id }}</td>
        <td>
            @if($permit->type === 'TP' || $permit instanceof \App\Models\TemporaryPermit)
                Temporary
            @elseif($permit->type === 'MP' || $permit instanceof \App\Models\MonthlyPermit)
                Monthly
            @elseif($permit->type === 'VH' || $permit instanceof \App\Models\VehiclePermit)
                Vehicle
            @endif
        </td>
        <td>
            @if($permit->type === 'VH' || $permit instanceof \App\Models\VehiclePermit)
                {{ $permit->owner_name }}
            @else
                {{ $permit->full_name }}
            @endif
        </td>
    </tr>
@endforeach
```

### Edit Button with Type
```blade
@php
    $permitType = match(true) {
        $permit instanceof \App\Models\TemporaryPermit => 'temporary',
        $permit instanceof \App\Models\MonthlyPermit => 'monthly',
        $permit instanceof \App\Models\VehiclePermit => 'vehicle',
        default => strtolower(str_replace('P', '', $permit->type ?? 'temporary'))
    };
@endphp

<a href="{{ route('permits.edit', [$permitType, $permit->id]) }}" 
   class="btn btn-sm btn-primary">
    Edit
</a>
```

### Delete Form with Type
```blade
<form action="{{ route('permits.destroy', [$permitType, $permit->id]) }}" 
      method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
</form>
```

---

## 🔍 Search & Filter Examples

### Search Across All Tables
```php
public function search(Request $request) {
    $query = $request->input('q');
    
    // Search temporary permits
    $temp = TemporaryPermit::where('full_name', 'like', "%$query%")
        ->orWhere('id_number', 'like', "%$query%")
        ->get();
    
    // Search monthly permits
    $monthly = MonthlyPermit::where('full_name', 'like', "%$query%")
        ->orWhere('id_number', 'like', "%$query%")
        ->get();
    
    // Search vehicle permits
    $vehicle = VehiclePermit::where('owner_name', 'like', "%$query%")
        ->orWhere('vehicle_number', 'like', "%$query%")
        ->get();
    
    $results = $temp->concat($monthly)->concat($vehicle);
    
    return view('search', compact('results'));
}
```

### Filter by Date Range
```php
$startDate = '2025-11-01';
$endDate = '2025-11-30';

$permits = collect();

$permits = $permits->concat(
    TemporaryPermit::whereBetween('from_date', [$startDate, $endDate])->get()
);
$permits = $permits->concat(
    MonthlyPermit::whereBetween('from_date', [$startDate, $endDate])->get()
);
$permits = $permits->concat(
    VehiclePermit::whereBetween('from_date', [$startDate, $endDate])->get()
);
```

---

## 📊 Dashboard Queries

### Total Permits by Type
```php
$stats = [
    'temporary' => TemporaryPermit::count(),
    'monthly' => MonthlyPermit::count(),
    'vehicle' => VehiclePermit::count(),
    'total' => TemporaryPermit::count() + MonthlyPermit::count() + VehiclePermit::count()
];
```

### Revenue by Type
```php
$revenue = [
    'temporary' => TemporaryPermit::sum(\DB::raw('rate + vat + IFNULL(ssl, 0)')),
    'monthly' => MonthlyPermit::sum(\DB::raw('rate + vat + IFNULL(ssl, 0)')),
    'vehicle' => VehiclePermit::sum(\DB::raw('rate + vat + IFNULL(ssl, 0)'))
];

$total_revenue = array_sum($revenue);
```

---

## 🎯 Common Patterns

### 1. Check Availability
```php
// Temporary Permit
$conflict = TemporaryPermit::where('id_number', $idNumber)
    ->whereBetween('from_date', [$from, $to])
    ->exists();

// Vehicle Permit
$conflict = VehiclePermit::where('vehicle_number', $vehicleNumber)
    ->whereBetween('from_date', [$from, $to])
    ->exists();
```

### 2. Generate Permit ID
```php
protected function generatePermitId($type) {
    $modelClass = match($type) {
        'TP' => TemporaryPermit::class,
        'MP' => MonthlyPermit::class,
        'VH' => VehiclePermit::class,
    };
    
    $latest = $modelClass::where('permit_id', 'like', $type . now()->format('y') . '%')
        ->orderBy('permit_id', 'desc')
        ->first();
    
    // ... generate new ID
}
```

### 3. Relationship with Payments
```php
// All models have payment relationship
$permit = TemporaryPermit::with('payment')->find($id);
$payment = $permit->payment; // returns Payment model or null
```

---

## 🔄 Migration Helpers

### Get Permit Type from String
```php
function getPermitTypeFromCode($code) {
    return match($code) {
        'TP' => 'temporary',
        'MP' => 'monthly',
        'VH' => 'vehicle',
        default => 'unknown'
    };
}
```

### Get Model Class from Type
```php
function getModelFromType($type) {
    return match($type) {
        'temporary' => TemporaryPermit::class,
        'monthly' => MonthlyPermit::class,
        'vehicle' => VehiclePermit::class,
        default => TemporaryPermit::class
    };
}
```

---

## ⚡ Performance Tips

1. **Use Indexes**: All important fields are indexed (permit_id, id_number, vehicle_number, company_name, status, dates)

2. **Eager Load Relationships**:
```php
$permits = TemporaryPermit::with('payment')->get();
```

3. **Query Only What You Need**:
```php
TemporaryPermit::select('id', 'permit_id', 'full_name', 'status')->get();
```

4. **Use Scopes**:
```php
$active = TemporaryPermit::active()->get(); // where('status', 'active')
$pending = MonthlyPermit::pending()->get(); // where('status', 'pending')
```

---

## 🐛 Troubleshooting

### Issue: Route not found
**Solution**: Clear route cache
```powershell
php artisan route:clear
php artisan route:cache
```

### Issue: Model not found
**Solution**: Check namespace and use statement
```php
use App\Models\TemporaryPermit;
use App\Models\MonthlyPermit;
use App\Models\VehiclePermit;
```

### Issue: View errors with permit type
**Solution**: Check if you're passing permitType to routes
```blade
{{ route('permits.edit', [$permitType, $permit->id]) }}
```

---

## 📚 Additional Resources

- **Models**: `app/Models/TemporaryPermit.php`, `MonthlyPermit.php`, `VehiclePermit.php`
- **Controllers**: `app/Http/Controllers/PermitController.php` and type-specific controllers
- **Migrations**: `database/migrations/2025_11_12_*`
- **Routes**: `routes/web.php`

---

**Last Updated**: November 12, 2025
**Version**: 2.0 (Separate Tables)
