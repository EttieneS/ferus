<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sender_id');
            $table->integer('sender_type');
            $table->integer('origin');
            $table->string('subject');
            $table->longText('body');
            $table->boolean('is_internal')->default(false);
            $table->unsignedBigInteger('in_reply_to')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['sender_id', 'sender_type']);
            $table->foreign('in_reply_to')->references('id')->on('mails')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('mails');
    }
};
