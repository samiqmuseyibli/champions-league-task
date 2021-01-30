<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('league_id');
            $table->string('name');
            $table->tinyInteger('percent');
            $table->tinyInteger('win')->default(0);
            $table->tinyInteger('draw')->default(0);
            $table->tinyInteger('lost')->default(0);
            $table->tinyInteger('gf')->default(0);
            $table->tinyInteger('ga')->default(0);
            $table->tinyInteger('gd')->default(0);
            $table->tinyInteger('points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clubs');
    }
}
