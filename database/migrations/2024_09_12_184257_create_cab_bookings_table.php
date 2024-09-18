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
        Schema::create('cab_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('bill_to')->comment('1 = company, 2 = client, 3 = matter expenses');
            $table->tinyInteger('booking_type')->comment('1 = one_way, 2 = hourly_rental, 3 = airport_transfer');
            $table->tinyInteger('cab_type')->comment('1 = hatchback, 2 = sedan, 3 = suv');
            $table->string('from_location');
            $table->string('to_location')->nullable();
            $table->string('departure_date')->nullable();
            $table->string('pickup_date')->nullable();
            $table->time('pickup_time');
            $table->integer('hours')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0 = pending, 1 = confirmed, 2 = cancelled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cab_bookings');
    }
};
