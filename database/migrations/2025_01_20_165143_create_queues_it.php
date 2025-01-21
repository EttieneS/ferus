<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{    
    public function up(): void {
        Schema::create('queues_it', function (Blueprint $table) {
            $table->bigIncrements('id')->primary()->startingValue(0);
            $table->bigInteger('ticket_id');
            $table->bigInteger('assigned_by');
            $table->bigInteger('assigned_to');
            $table->integer('status');
            $table->integer('priority');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void {        
        Schema::dropIfExists('queues_it');        
    }
};
