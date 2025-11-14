<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\TemporaryPermit;
use App\Models\MonthlyPermit;
use App\Models\VehiclePermit;
use App\Models\Payment;

class IdGeneratorHelper
{
    /**
     * Generate unique submission ID with database lock to prevent collisions
     * Format: A + YYMMDD + count (e.g., A25111201)
     * 
     * @return string
     */
    public static function generateSubmissionId(): string
    {
        $datePrefix = 'A' . now()->format('ymd');

        // Use advisory lock
        $lockName = 'submission_id_generation';
        $lockResult = DB::selectOne("SELECT GET_LOCK(?, 10) as locked", [$lockName]);

        if (!$lockResult || !$lockResult->locked) {
            throw new \Exception('Could not obtain lock for submission ID generation');
        }

        try {
            // Get latest from all permit tables
            $latestTemp = TemporaryPermit::where('submission_id', 'like', $datePrefix . '%')
                ->orderBy('submission_id', 'desc')
                ->first();

            $latestMonthly = MonthlyPermit::where('submission_id', 'like', $datePrefix . '%')
                ->orderBy('submission_id', 'desc')
                ->first();

            $latestVehicle = VehiclePermit::where('submission_id', 'like', $datePrefix . '%')
                ->orderBy('submission_id', 'desc')
                ->first();

            $latestPayment = Payment::where('submission_id', 'like', $datePrefix . '%')
                ->orderBy('submission_id', 'desc')
                ->first();

            // Find the highest counter
            $nextNumber = 1;

            if ($latestTemp) {
                $counter = (int) substr($latestTemp->submission_id, -2);
                $nextNumber = max($nextNumber, $counter + 1);
            }

            if ($latestMonthly) {
                $counter = (int) substr($latestMonthly->submission_id, -2);
                $nextNumber = max($nextNumber, $counter + 1);
            }

            if ($latestVehicle) {
                $counter = (int) substr($latestVehicle->submission_id, -2);
                $nextNumber = max($nextNumber, $counter + 1);
            }

            if ($latestPayment) {
                $counter = (int) substr($latestPayment->submission_id, -2);
                $nextNumber = max($nextNumber, $counter + 1);
            }

            $submissionId = $datePrefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

            return $submissionId;
        } finally {
            DB::selectOne("SELECT RELEASE_LOCK(?) as released", [$lockName]);
        }
    }

    /**
     * Generate unique permit ID with database lock to prevent collisions
     * Format: TYPE + YY + MM + count (e.g., TP2511001)
     * 
     * @param string $type TP, MP, or VP
     * @return string
     */
    public static function generatePermitId(string $type): string
    {
        $yearPrefix = now()->format('y');
        $month = now()->format('m');
        $searchPattern = $type . $yearPrefix . $month . '%';

        // Determine model based on type
        switch ($type) {
            case 'TP':
                $model = TemporaryPermit::class;
                break;
            case 'MP':
                $model = MonthlyPermit::class;
                break;
            case 'VP':
                $model = VehiclePermit::class;
                break;
            default:
                throw new \Exception("Invalid permit type: $type");
        }

        // Use advisory lock specific to permit type
        $lockName = 'permit_id_generation_' . $type;
        $lockResult = DB::selectOne("SELECT GET_LOCK(?, 10) as locked", [$lockName]);

        if (!$lockResult || !$lockResult->locked) {
            throw new \Exception('Could not obtain lock for permit ID generation');
        }

        try {
            // Get the latest permit ID for this type/year/month
            $latest = $model::where('permit_id', 'like', $searchPattern)
                ->orderBy('permit_id', 'desc')
                ->first();

            $nextNumber = 1;

            if ($latest) {
                $lastCounter = (int) substr($latest->permit_id, -4);
                $nextNumber = $lastCounter + 1;
            }

            $permitId = $type . $yearPrefix . $month . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            return $permitId;
        } finally {
            DB::selectOne("SELECT RELEASE_LOCK(?) as released", [$lockName]);
        }
    }

    /**
     * Generate unique application number with database lock to prevent collisions
     * Format: AP + YYMMDD + count (e.g., AP251112150)
     * 
     * @return string
     */
    public static function generateApplicationNumber(): string
    {
        $numbers = self::generateMultipleApplicationNumbers(1);
        return $numbers[0];
    }

    /**
     * Generate multiple unique application numbers in a single lock
     * This prevents duplicate application numbers when generating for multiple entries
     * 
     * @param int $count Number of application numbers to generate
     * @return array Array of application numbers
     */
    public static function generateMultipleApplicationNumbers(int $count): array
    {
        $datePrefix = 'AP' . now()->format('ymd');

        // Use advisory lock
        $lockName = 'app_number_generation';
        $lockResult = DB::selectOne("SELECT GET_LOCK(?, 10) as locked", [$lockName]);

        if (!$lockResult || !$lockResult->locked) {
            throw new \Exception('Could not obtain lock for application number generation');
        }

        try {
            // Check all permit tables for the highest application number today
            // Use MAX aggregation for better performance and reliability
            // Include soft-deleted records to prevent number reuse
            $maxTemp = DB::table('temporary_permits')
                ->where('application_number', 'like', $datePrefix . '%')
                ->whereNull('deleted_at') // Only active records
                ->max('application_number');

            $maxMonthly = DB::table('monthly_permits')
                ->where('application_number', 'like', $datePrefix . '%')
                ->whereNull('deleted_at') // Only active records
                ->max('application_number');

            $maxVehicle = DB::table('vehicle_permits')
                ->where('application_number', 'like', $datePrefix . '%')
                ->whereNull('deleted_at') // Only active records
                ->max('application_number');

            // Find the highest counter from all tables
            $nextNumber = 1;
            $allMaxNumbers = array_filter([$maxTemp, $maxMonthly, $maxVehicle]);

            if (!empty($allMaxNumbers)) {
                foreach ($allMaxNumbers as $maxNumber) {
                    if ($maxNumber) {
                        $counter = (int) substr($maxNumber, -3);
                        $nextNumber = max($nextNumber, $counter + 1);
                    }
                }
            }

            // Generate multiple sequential application numbers
            $applicationNumbers = [];
            for ($i = 0; $i < $count; $i++) {
                $applicationNumbers[] = $datePrefix . str_pad($nextNumber + $i, 3, '0', STR_PAD_LEFT);
            }

            return $applicationNumbers;
        } finally {
            DB::selectOne("SELECT RELEASE_LOCK(?) as released", [$lockName]);
        }
    }

    /**
     * Generate unique invoice ID with database lock to prevent collisions
     * Format: INV + YYMMDD + count (e.g., INV251112001)
     * 
     * @return string
     */
    public static function generateInvoiceId(): string
    {
        $datePrefix = 'INV' . now()->format('ymd');

        // Use advisory lock
        $lockName = 'invoice_id_generation';
        $lockResult = DB::selectOne("SELECT GET_LOCK(?, 10) as locked", [$lockName]);

        if (!$lockResult || !$lockResult->locked) {
            throw new \Exception('Could not obtain lock for invoice ID generation');
        }

        try {
            $latest = Payment::where('invoice_id', 'like', $datePrefix . '%')
                ->orderBy('invoice_id', 'desc')
                ->first();

            $nextNumber = 1;

            if ($latest) {
                $counter = (int) substr($latest->invoice_id, -3);
                $nextNumber = $counter + 1;
            }

            $invoiceId = $datePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            return $invoiceId;
        } finally {
            DB::selectOne("SELECT RELEASE_LOCK(?) as released", [$lockName]);
        }
    }
}
