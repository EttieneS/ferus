<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('incoming_mails', function (Blueprint $table) {
            $table->bigIncrements('id')->startingValue(0);
            $table->bigInteger('customer_id')->unsigned()->index();
            $table->string('message_id');
            $table->string('subject');
            $table->text('body');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
        });
    }

    public function down() {
        Schema::dropIfExists('incoming_mails');
    }
};
