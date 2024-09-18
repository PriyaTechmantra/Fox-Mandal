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
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->tinyInteger('trip_type')->comment('1 = one way, 2 = round trip'); 
            $table->string('from'); 
            $table->string('to'); 
            $table->string('preference_departure_date'); 
            $table->string('preference_arrival_time'); 
            $table->string('departure_date'); 
            $table->string('return_date')->nullable(); 
            $table->unsignedInteger('traveler_number'); 
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
        Schema::dropIfExists('flight_bookings');
    }
};
