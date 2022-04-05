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
        Schema::create('sells', function (Blueprint $table) {
            $table->id();
            $table->string("medicine");
            $table->foreignId("customer_id")->constrained();
            $table->integer("sell_id")->nullable();
            $table->float("qty");
            $table->float("price_per_peice");
            $table->float("total_price");
            $table->float("gst");
            $table->string("brand");
            $table->string("category");
            $table->string("pharmacist");
            $table->string("seller");
            $table->string("image_link");
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
        Schema::dropIfExists('sells');
    }
};
