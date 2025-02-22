<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Modify users table to ensure id is BIGINT
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->change();
        });

        // Create queues table before assigned_tickets
        Schema::create('queues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('assigned_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('queue_id')->constrained('queues')->onDelete('cascade');
            $table->unsignedBigInteger('assigned_by');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending');
            $table->string('priority')->default('normal');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down() {
        Schema::dropIfExists('assigned_tickets');
        Schema::dropIfExists('queues');
        
        // Revert users table change
        Schema::table('users', function (Blueprint $table) {
            $table->integer('id')->change();
        });
    }
};
