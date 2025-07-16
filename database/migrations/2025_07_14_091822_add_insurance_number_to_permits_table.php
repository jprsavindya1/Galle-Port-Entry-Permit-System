<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('permits', function (Blueprint $table) {
        $table->string('insurance_number')->nullable()->after('revenue_license_number');
    });
}

public function down()
{
    Schema::table('permits', function (Blueprint $table) {
        $table->dropColumn('insurance_number');
    });
}

};
