<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('queues', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned()->startingValue(0);
            $table->string('name')->unique();
            $table->string('mailer')->unique();
            $table->foreignId('sla_id')->nullable()->constrained('slas')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('queues');
    }
};
