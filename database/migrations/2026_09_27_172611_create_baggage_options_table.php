<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baggage_options', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->unsignedSmallInteger('weight_kg');

            $table->decimal('price', 10, 2)->default(0);

            $table->boolean('is_included')->default(false);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baggage_options');
    }
};