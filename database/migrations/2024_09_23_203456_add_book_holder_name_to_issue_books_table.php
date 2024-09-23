<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('issue_books', function (Blueprint $table) {
            $table->string('user_id2')->after('book_holder_user_id');// Add the new column after 'book_holder_user_id'
            $table->string('name_of_issue_person')->after('user_id2'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issue_books', function (Blueprint $table) {
            $table->dropColumn('book_holder_name');
        });
    }
};
