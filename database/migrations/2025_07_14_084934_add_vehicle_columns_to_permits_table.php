<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('permits', function (Blueprint $table) {
        $table->string('vehicle_type')->nullable();
        $table->string('vehicle_number')->nullable();
        $table->string('revenue_license_number')->nullable();
        $table->string('owner_name')->nullable();
        $table->string('owner_address')->nullable();
        $table->text('remarks')->nullable();
    });
}

public function down()
{
    Schema::table('permits', function (Blueprint $table) {
        $table->dropColumn([
            'vehicle_type',
            'vehicle_number',
            'revenue_license_number',
            'owner_name',
            'owner_address',
            'remarks',
        ]);
    });
}

};
