<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('home_id');
            $table->unsignedBigInteger('away_id');
            $table->integer('league_id');
            $table->integer('tour');
            $table->dateTime('played_at')->nullable();
            $table->integer('home_goal_count')->nullable();
            $table->integer('away_goal_count')->nullable();
            $table->timestamps();
        });

        Schema::table('fixtures', function (Blueprint $table) {
            $table->foreign('home_id')->references('id')->on('clubs');
            $table->foreign('away_id')->references('id')->on('clubs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixtures');
    }
}
