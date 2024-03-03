<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commodities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol')->unique();
            $table->timestamps();
        });

        Schema::create('commodity_rates', function (Blueprint $table) {
            $table->unsignedBigInteger('commodity_id')->references('id')->on('Commodities')->index();
            $table->decimal('rate', 10, 4);
            $table->dateTime('datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commodities');
        Schema::dropIfExists('commodity_rates');
    }
};
