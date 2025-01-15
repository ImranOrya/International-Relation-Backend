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
        Schema::create('user_detail_trans', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->unsignedBigInteger('user_detail_id');
            $table->foreign('user_detail_id')->references('id')->on('user_details')
                ->onUpdate('cascade')
                ->onDelete('no action');
                   $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_detail_trans');
    }
};
