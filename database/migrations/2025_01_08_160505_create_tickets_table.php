<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('incoming_mail_id')->unsigned();
            $table->bigInteger('queue_id')->unsigned();
            $table->bigInteger('assigned_by')->unsigned()->nullable();
            $table->bigInteger('assigned_to')->unsigned()->nullable();
            $table->integer('status')->default(0);
            $table->integer('priority')->default(0);
            $table->boolean('split')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('incoming_mail_id')->references('id')->on('incoming_mails')->onDelete('restrict'); // Only an incoming mail can create a ticket
            $table->foreign('queue_id')->references('id')->on('queues')->onDelete('restrict');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('restrict')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('restrict')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
