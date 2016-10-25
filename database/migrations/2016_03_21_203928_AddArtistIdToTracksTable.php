<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArtistIdToTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->integer('artist_id')->after('user_id')->nullable()->index();
        });
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropColumn('artist');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracks', function (Blueprint $table) {
            $table->dropColumn('artist_id');
        });
        Schema::table('tracks', function (Blueprint $table) {
            $table->string('artist')->after('spotify_id')->nullable();
        });
    }
}
