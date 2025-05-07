<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('mail_recipients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('mail_id')->constrained('mails')->onDelete('restrict');
            $table->unsignedBigInteger('recipient_id');
            $table->unsignedTinyInteger('recipient_type');
            $table->unsignedTinyInteger('send_type');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['recipient_id', 'recipient_type']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('mail_recipients');
    }
};
