<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {    
    public function up(): void {
        Schema::create('mail_bodies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mail_id')->unique();
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->timestamps();
            $table->foreign('mail_id')->references('id')->on('mails')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('mail_bodies');
    }
};
