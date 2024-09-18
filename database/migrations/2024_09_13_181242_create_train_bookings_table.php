<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('train_bookings', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('from'); 
            $table->string('to'); 
            $table->string('travel_date'); 
            $table->tinyInteger('bill_to')->comment('1 = company, 2 = client, 3 = matter expenses');
            $table->tinyInteger('status')->default(0)->comment('0 = pending, 1 = confirmed, 2 = cancelled');
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('train_bookings');
    }
};
