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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');  
            $table->string('type');  
            $table->string('address');  
            $table->decimal('rent', 10, 2);  
            $table->integer('bedrooms')->nullable();  
            $table->integer('bathrooms')->nullable();  
            $table->integer('floor_area')->nullable();  
            $table->boolean('is_available')->default(true);  
            $table->text('description')->nullable(); 
            $table->string('image')->nullable();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
