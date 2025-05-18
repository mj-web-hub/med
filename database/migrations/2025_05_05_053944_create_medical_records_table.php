<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('chronic_conditions')->nullable();
            $table->text('previous_illnesses')->nullable();
            $table->text('surgeries_hospitalizations')->nullable();
            $table->text('allergies')->nullable();
            $table->text('immunization_history')->nullable();
            $table->text('childhood_illnesses')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medical_records');
    }
};