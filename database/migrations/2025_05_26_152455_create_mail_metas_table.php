<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailMetasTable extends Migration {
    public function up(): void {
        Schema::create('mail_metas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mail_id')->unique();
            $table->unsignedBigInteger('in_reply_to')->nullable();

            $table->json('toUsers')->nullable();
            $table->json('ccUsers')->nullable();
            $table->json('toCustomers')->nullable();
            $table->json('ccCustomers')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('mail_id')->references('id')->on('mails')->onDelete('cascade');
            $table->foreign('in_reply_to')->references('id')->on('mails')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('mail_metas');
    }
}
