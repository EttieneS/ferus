<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('from');
            $table->string('full_name');
            $table->string('subject');                        
        });
    }
 
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('from');
            $table->dropColumn('full_name');                        
            $table->dropColumn('subject');                        
        });
    }
};
