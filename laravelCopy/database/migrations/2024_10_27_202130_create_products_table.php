<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('description',200);
            $table->string('product_image',400);
            $table->decimal('cost_price',10, 2)->nullable();
            $table->integer('increase')->nullable();
            $table->bigInteger('stock')->nullable();
            $table->boolean('enabled')->default(true);
            $table->unsignedbigInteger('user_created');
            $table->foreign('user_created')->references('id')->on('users');
            $table->unsignedbigInteger('user_updated');
            $table->foreign('user_updated')->references('id')->on('users');
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
}
