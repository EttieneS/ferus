<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('mail_id');
            $table->unsignedBigInteger('queue_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->integer('status')->default(0);
            $table->integer('priority')->default(0);
            $table->boolean('split')->default(false);
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('mail_id')->references('id')->on('mails')->onDelete('restrict');
            $table->foreign('queue_id')->references('id')->on('queues')->onDelete('restrict');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('restrict');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
