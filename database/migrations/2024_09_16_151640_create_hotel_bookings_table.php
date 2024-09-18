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
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('room_id'); 
            $table->unsignedBigInteger('property_id'); 
            $table->string('checkin_date');
            $table->string('checkout_date'); 
            $table->integer('guest_number');
            $table->integer('room_number');
            $table->tinyInteger('status')->default(0)->comment('0 = pending, 1 = confirmed, 2 = cancelled');
            $table->tinyInteger('bill_to')->comment('1 = company, 2 = client, 3 = matter expenses');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
