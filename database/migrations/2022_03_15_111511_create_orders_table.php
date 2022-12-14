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


            $table->float('service_price');
            $table->string('type')->nullable();
            $table->string('payment_number')->nullable();
            $table->string('TrxID')->nullable();
            $table->text('name');
            $table->text('email');
            $table->text('phone');
            $table->text('address')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('service_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
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
