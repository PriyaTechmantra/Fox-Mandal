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
            $table->tinyInteger('booking_type')->comment('1 = one_way, 2 = hourly_rental, 3 = airport_transfer');
            $table->string('from_location');
            $table->string('to_location')->nullable();
            // $table->date('departure_date')->nullable();
            $table->string('departure_date')->nullable();
            $table->string('pickup_date')->nullable();

            $table->time('pickup_time');
            $table->integer('hours')->nullable();
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
