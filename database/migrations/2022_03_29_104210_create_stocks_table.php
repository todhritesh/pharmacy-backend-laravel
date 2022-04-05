<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId('user_id')->constrained();
            $table->string("brand");
            $table->string("category");
            $table->integer("total_qty");
            $table->integer("remaining_qty");
            $table->date("mfd");
            $table->date("exp");
            $table->float("price_per_peice");
            $table->float("total_cost");
            $table->float("sp");
            $table->float("gst");
            $table->string("image")->nullable();
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
        Schema::dropIfExists('stocks');
    }
};
