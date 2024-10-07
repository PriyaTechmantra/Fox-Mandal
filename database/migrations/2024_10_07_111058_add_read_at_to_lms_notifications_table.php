<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReadAtToLmsNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lms_notifications', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('is_read'); // Adding nullable read_at column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lms_notifications', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
}
