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
        Schema::table('users', function (Blueprint $table) {
            $table->string('restaurant_name')->after('full_name')->nullable();
            $table->text('restaurant_description')->after('restaurant_address')->nullable();
            $table->string('opening_hours')->after('cuisine_type')->nullable()->default('L-D: 9:00 AM - 10:00 PM');
            $table->string('profile_image_url')->after('vehicle_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
