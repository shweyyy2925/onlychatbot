<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('chat_histories', function (Blueprint $table) {
        $table->id();
        $table->text('question');
        $table->text('answer');
        $table->timestamps();
    });
}

};
