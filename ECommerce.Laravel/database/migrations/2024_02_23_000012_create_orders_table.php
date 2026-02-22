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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('shipping_address');
            $table->text('delivery_details')->nullable();
            $table->decimal('sub_total', 18, 2);
            $table->decimal('tax', 18, 2)->default(0);
            $table->decimal('shipping_cost', 18, 2);
            $table->decimal('total', 18, 2);
            $table->foreignId('delivery_method_id')->nullable()->constrained('delivery_methods')->onDelete('set null');
            $table->string('status')->default('Pending'); // Enum equivalent
            $table->bigInteger('steadfast_consignment_id')->nullable();
            $table->string('steadfast_tracking_code')->nullable();
            $table->string('steadfast_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
