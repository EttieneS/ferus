<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('message_id')->unique()->after('id');
            $table->renameColumn('description', 'subject');
        });
    }

    public function down(): void {
        Schema::table('tickets', function (Blueprint $table) {
            $table->renameColumn('subject', 'description');
            $table->dropColumn('message_id');
        });
    }
};
