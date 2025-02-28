<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('queued_tickets', function (Blueprint $table) {
            $table->bigIncrements('id')->startingValue(0);
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('queue_id')->nullable();            
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();            
            $table->integer('status')->default(0);
            $table->integer('priority')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');            
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('queued_tickets');
    }
};
