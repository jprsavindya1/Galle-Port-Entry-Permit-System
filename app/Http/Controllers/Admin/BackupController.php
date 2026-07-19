<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    /**
     * Show backup dashboard.
     */
    public function index()
    {
        $databaseName = config('database.connections.mysql.database');
        
        $tables = DB::select("
            SELECT 
                TABLE_NAME AS `table`, 
                TABLE_ROWS AS `rows`, 
                ROUND(((data_length + index_length) / 1024), 2) AS `size` 
            FROM information_schema.TABLES 
            WHERE table_schema = ?
        ", [$databaseName]);

        $totalSize = array_sum(array_column($tables, 'size'));
        $totalRows = array_sum(array_column($tables, 'rows'));
        $tableCount = count($tables);

        return view('admin.backup.index', compact('tables', 'totalSize', 'totalRows', 'tableCount', 'databaseName'));
    }

    /**
     * Generate and download SQL backup.
     */
    public function download()
    {
        $databaseName = config('database.connections.mysql.database');
        $filename = $databaseName . '_backup_' . date('Y-m-d_H-i-s') . '.sql';

        $headers = [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($databaseName) {
            $tables = DB::select('SHOW TABLES');
            $key = 'Tables_in_' . $databaseName;

            echo "-- Database Backup: " . $databaseName . "\n";
            echo "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
            echo "-- SLPA Port Entry Permit System\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $tableName = $table->$key;

                // 1. Get Table Structure
                $createTable = DB::select("SHOW CREATE TABLE `$tableName`")[0];
                $createTableKey = 'Create Table';
                $sql = $createTable->$createTableKey;

                echo "-- Table structure for table `$tableName`\n";
                echo "DROP TABLE IF EXISTS `$tableName`;\n";
                echo "$sql;\n\n";

                // 2. Get Table Data
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    echo "-- Dumping data for table `$tableName`\n";
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $columns = array_keys($rowArray);
                        $escapedColumns = array_map(fn($col) => "`$col`", $columns);
                        
                        $values = [];
                        foreach ($rowArray as $val) {
                            if ($val === null) {
                                $values[] = 'NULL';
                            } else {
                                $values[] = "'" . addslashes($val) . "'";
                            }
                        }
                        
                        echo "INSERT INTO `$tableName` (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $values) . ");\n";
                    }
                    echo "\n";
                }
            }

            echo "SET FOREIGN_KEY_CHECKS=1;\n";
        };

        // Log action using ActivityLogHelper
        \App\Helpers\ActivityLogHelper::logActivity('Downloaded Database SQL Backup', null, null, [
            'database' => $databaseName,
            'filename' => $filename,
        ]);

        return new StreamedResponse($callback, 200, $headers);
    }
}
