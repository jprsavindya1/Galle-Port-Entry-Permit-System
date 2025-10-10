<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blacklist_histories', function (Blueprint $table) {
            $table->string('reinstated_by')->nullable()->after('admin_role');
            $table->timestamp('reinstated_on')->nullable()->after('reinstated_by');
            $table->string('status')->default('Deleted')->after('reinstated_on');
        });
    }

    public function down(): void
    {
        Schema::table('blacklist_histories', function (Blueprint $table) {
            $table->dropColumn(['reinstated_by', 'reinstated_on', 'status']);
        });
    }
};
