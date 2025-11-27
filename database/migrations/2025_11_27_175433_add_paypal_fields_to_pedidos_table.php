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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('paypal_order_id')->nullable()->after('total');
            $table->string('paypal_payer_id')->nullable()->after('paypal_order_id');
            $table->string('paypal_payment_status')->nullable()->after('paypal_payer_id');
            $table->string('paypal_capture_id')->nullable()->after('paypal_payment_status');
            $table->string('metodo_pago')->default('paypal')->after('paypal_capture_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'paypal_order_id',
                'paypal_payer_id',
                'paypal_payment_status',
                'paypal_capture_id',
                'metodo_pago'
            ]);
        });
    }
};
