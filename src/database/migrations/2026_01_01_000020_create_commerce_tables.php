<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price'); // kobo snapshot
            $table->timestamps();
        });

        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('states')->nullable(); // comma-separated or JSON list of states/areas
            $table->unsignedBigInteger('fee')->default(0); // kobo
            $table->string('estimated_delivery')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type')->default('percentage'); // percentage, fixed, free_delivery
            $table->unsignedBigInteger('value')->default(0); // percent 0-100 or kobo
            $table->unsignedBigInteger('min_order_value')->nullable(); // kobo
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('per_customer_limit')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sold_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel')->default('online'); // online, pos
            $table->string('payment_method')->nullable(); // paystack, cash, transfer, pos_terminal, other
            $table->string('customer_name')->nullable(); // nullable: POS walk-ins
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->text('order_notes')->nullable();
            $table->unsignedBigInteger('subtotal'); // kobo
            $table->unsignedBigInteger('delivery_fee')->default(0); // kobo
            $table->unsignedBigInteger('discount')->default(0); // kobo
            $table->unsignedBigInteger('total'); // kobo
            $table->string('currency', 3)->default('NGN');
            $table->string('payment_status')->default('pending'); // pending, paid, failed, abandoned, refunded
            $table->string('fulfilment_status')->default('unfulfilled'); // unfulfilled, processing, ready, shipped, delivered, cancelled
            $table->string('payment_reference')->nullable()->index();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('delivery_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tracking_token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('variant_summary')->nullable();
            $table->unsignedBigInteger('unit_price'); // kobo
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total'); // kobo
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('amount'); // kobo
            $table->string('currency', 3)->default('NGN');
            $table->string('status')->default('pending'); // pending, success, failed, abandoned
            $table->string('channel')->nullable();
            $table->text('gateway_response')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_email')->nullable();
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('field'); // payment_status or fulfilment_status
            $table->string('from')->nullable();
            $table->string('to');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('delivery_zones');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
