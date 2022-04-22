<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('order_number');
            $table->enum('status',['pending','processing','completed','declined'])->default('pending');
            $table->integer('item_count');
            $table->boolean('is_paid')->default(false);
            $table->enum('payment_method',['cash_on_delivery','paypal','stripe','card'])->default('cash_on_delivery');
            $table->float('total');

            //Billing Address
            $table->string('billing_first_name');
            $table->string('billing_last_name');
            $table->string('billing_company')->nullable();
            $table->string('billing_country_region');
            $table->string('billing_address_street');
            $table->string('billing_address_apartment')->nullable();
            $table->string('billing_town_city');
            $table->string('billing_zip');
            $table->string('billing_state');
            $table->string('billing_phone');
            $table->string('billing_email_address');
            $table->boolean('billing_ship_to_a_different_address')->default(false);

             //Shipping Address
             $table->string('shipping_first_name');
             $table->string('shipping_last_name');
             $table->string('shipping_company')->nullable();
             $table->string('shipping_country_region');
             $table->string('shipping_address_street');
             $table->string('shipping_address_apartment')->nullable();
             $table->string('shipping_town_city');
             $table->string('shipping_zip');
             $table->string('shipping_state');
             $table->string('shipping_phone');
             $table->string('shipping_email_address');
             $table->boolean('shipping_ship_to_a_different_address')->default(false);
            $table->string('order_notes')->nullable();

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
        Schema::dropIfExists('orders');
    }
}
