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
        $table->date('police_report_issue_date')->nullable();
        $table->date('police_report_expire_date')->nullable();
    });
}

public function down()
{
    Schema::table('permits', function (Blueprint $table) {
        $table->dropColumn(['police_report_issue_date', 'police_report_expire_date']);
    });
}

};
