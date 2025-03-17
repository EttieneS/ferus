<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {    
    public function up(): void {
        Schema::create('mails', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->startingValue(0);                
            $table->string('subject');
            $table->text('message')->nullable();
            $table->string('message_id');            
            $table->bigInteger('customer_id')->unsigned();            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
        });
    }

    public function down(): void {
        Schema::dropIfExists('mails');  
    }
};
