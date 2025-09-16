<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            if (Schema::hasColumn('payment_settings', 'ssl')) {
                $table->dropColumn('ssl');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'ssl_total')) {
                $table->dropColumn('ssl_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->decimal('ssl', 8, 2)->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('ssl_total', 10, 2)->default(0);
        });
    }
};
