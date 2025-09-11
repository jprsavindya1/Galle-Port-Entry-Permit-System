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
    Schema::table('vehicles', function (Blueprint $table) {
        if (!Schema::hasColumn('vehicles', 'code')) {
            $table->string('code')->unique()->after('id');
        }
        if (!Schema::hasColumn('vehicles', 'rate')) {
            $table->decimal('rate', 10, 2)->default(0)->after('name');
        }
    });
}

};
