<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('email');
            $table->decimal('bill', 10, 2);
            $table->string('phone');
            $table->text('address');
            $table->string('customer_status')->default('Active');
            $table->enum('status', ['Pending', 'Accepted', 'Rejected', 'Delivered'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
