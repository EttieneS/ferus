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

            $table->string('host')->nullable();
            $table->integer('port')->nullable();
            $table->string('encryption')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();

            $table->foreignId('sla_id')->nullable()
                ->constrained('slas')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('queues');
    }
};
