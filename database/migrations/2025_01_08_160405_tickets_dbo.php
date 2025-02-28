<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {    
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->startingValue(0);                
            $table->string('subject');
            $table->text('message')->nullable();
            $table->string('message_id');            
            $table->bigInteger('customer_id')->unsigned();            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('queues');
    }
};
