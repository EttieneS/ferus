<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('ticket_id')->constrained()->onDelete('restrict');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('user_type'); // 0 = user, 1 = customer
            $table->unsignedTinyInteger('mail_type'); // 0 = incoming, 1 = outgoing
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'user_type']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('mails');
    }
};
