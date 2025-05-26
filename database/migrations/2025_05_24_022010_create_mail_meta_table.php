<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mail_meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mail_id')->unique();

            $table->json('to_user_ids')->nullable();
            $table->json('cc_user_ids')->nullable();
            $table->json('to_customer_ids')->nullable();
            $table->json('cc_customer_ids')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('mail_id')->references('id')->on('mails')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('mail_meta');
    }
};
