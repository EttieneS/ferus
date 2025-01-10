<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{    
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigInteger('id')->primary()->startingValue(0);
            $table->string('title');
            $table->string('description');
            $table->integer('priority')->default(0);
            $table->integer('status')->default(0);
            $table->integer('member_id')->default(0);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
