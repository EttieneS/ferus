<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['from', 'full_name']);
            $table->unsignedBigInteger('customer_id')->nullable()->after('id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('from')->nullable();
            $table->string('full_name')->nullable();
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
