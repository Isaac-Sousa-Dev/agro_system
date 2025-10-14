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
        Schema::create('production_units', function (Blueprint $table) {
            $table->id();
            $table->string('crop_name');
            $table->decimal('total_area_ha', 10, 2);
            $table->string('geographic_coordinates')->nullable();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_units');
    }
};
