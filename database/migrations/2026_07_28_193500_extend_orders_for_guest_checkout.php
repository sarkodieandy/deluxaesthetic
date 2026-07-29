<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('guest_email')->nullable()->after('user_id');
            $table->string('guest_phone')->nullable()->after('guest_email');
            $table->string('guest_name')->nullable()->after('guest_phone');
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->timestamp('paid_at')->nullable()->after('notes');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index('guest_email');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->string('fulfillment_type')->nullable()->after('coupon_code');
            $table->unsignedBigInteger('branch_id')->nullable()->after('fulfillment_type');
            $table->json('checkout_contact')->nullable()->after('branch_id');
            $table->json('checkout_address')->nullable()->after('checkout_contact');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['fulfillment_type', 'branch_id', 'checkout_contact', 'checkout_address']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['guest_email']);
            $table->dropColumn(['guest_email', 'guest_phone', 'guest_name', 'payment_status', 'paid_at']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
