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
    Schema::create('faq', function (Blueprint $table) {
        $table->id(); // id INT PRIMARY KEY IDENTITY(1,1)
        $table->string('question', 255);
        $table->text('answer'); // text es para textos largos como NVARCHAR(MAX)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq');
    }
};
