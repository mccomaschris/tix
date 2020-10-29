<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->float('points', 12, 4);
            $table->float('annual_fund', 12, 4);
            $table->integer('herd_athletic_fund')->default(0);
            $table->integer('tip_off_club')->default(0);
            $table->string('herd_rises')->default(0);
            $table->integer('tickets_alloted')->nullable();
            $table->integer('rank');
            $table->integer('balls')->default(0);
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
        Schema::dropIfExists('customers');
    }
}
