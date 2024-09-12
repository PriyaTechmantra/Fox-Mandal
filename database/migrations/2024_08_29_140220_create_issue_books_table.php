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
        Schema::create('issue_books', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('book_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->date('request_date'); 
            $table->tinyInteger('status')->nullable()->comment('0 = reject, 1 = approve'); 
            $table->date('approve_date')->nullable(); 
            $table->timestamps(); 
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_books');
    }
};
