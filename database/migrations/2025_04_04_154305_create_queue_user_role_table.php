<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('queue_user_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('queue_id')->constrained('queues')->onDelete('restrict')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->nullable();
            $table->foreignId('role_id')->constrained('user_roles')->onDelete('restrict')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['queue_id', 'user_id', 'role_id'], 'idx_queue_user_role');            
        });
    }

    public function down(): void {
        Schema::dropIfExists('queue_user_role');
    }
};
