<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('health_booking');
        Schema::dropIfExists('health_service');
        Schema::dropIfExists('payment');
        Schema::dropIfExists('order_detail');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product');
        Schema::dropIfExists('adoption_request');
        Schema::dropIfExists('animal');
        Schema::dropIfExists('pet');
        Schema::dropIfExists('user');
        Schema::enableForeignKeyConstraints();

        Schema::create('user', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('name', 100);
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('bio', 255)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('pet', function (Blueprint $table) {
            $table->increments('pet_id');
            $table->unsignedInteger('user_id');
            $table->string('pet_name', 100);
            $table->string('species', 50);
            $table->string('breed', 50)->default('Tidak diketahui');
            $table->string('gender', 20)->default('Tidak diketahui');
            $table->string('age', 30)->default('1 Tahun');
            $table->string('health_status', 50)->default('Sehat');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user')->cascadeOnDelete();
        });

        Schema::create('animal', function (Blueprint $table) {
            $table->increments('animal_id');
            $table->string('nama', 100);
            $table->string('species', 50);
            $table->string('breed', 50);
            $table->string('gender', 20);
            $table->string('age', 50);
            $table->enum('status', ['available', 'adopted', 'unavailable'])->default('available');
            $table->string('image', 255)->default('assets/img/meng.png');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('adoption_request', function (Blueprint $table) {
            $table->increments('adoption_request_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('animal_id');
            $table->string('applicant_name', 100);
            $table->string('email', 100);
            $table->string('phone', 30);
            $table->text('address');
            $table->text('reason');
            $table->dateTime('request_date')->useCurrent();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user')->cascadeOnDelete();
            $table->foreign('animal_id')->references('animal_id')->on('animal')->cascadeOnDelete();
        });

        Schema::create('product', function (Blueprint $table) {
            $table->increments('product_id');
            $table->string('slug', 100)->unique();
            $table->string('product_name', 150);
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);
            $table->string('category', 50);
            $table->string('image', 255)->default('assets/img/shop/whiskas.png');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->string('order_code', 40)->unique();
            $table->unsignedInteger('user_id');
            $table->string('recipient_name', 100);
            $table->string('recipient_phone', 30);
            $table->enum('delivery_method', ['pickup', 'delivery'])->default('pickup');
            $table->text('shipping_address')->nullable();
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->dateTime('order_date')->useCurrent();
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user')->cascadeOnDelete();
        });

        Schema::create('order_detail', function (Blueprint $table) {
            $table->increments('order_detail_id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
            $table->foreign('product_id')->references('product_id')->on('product')->restrictOnDelete();
        });

        Schema::create('payment', function (Blueprint $table) {
            $table->increments('payment_id');
            $table->unsignedInteger('order_id');
            $table->dateTime('payment_date')->useCurrent();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->default('Simulasi Checkout');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('paid');
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->cascadeOnDelete();
        });

        Schema::create('health_service', function (Blueprint $table) {
            $table->increments('service_id');
            $table->string('service_name', 100);
            $table->string('doctor_name', 100)->default('drh. Clara Wijaya');
            $table->string('practice_days', 100)->default('Senin - Sabtu');
            $table->string('time_slots', 150)->default('09:00, 13:00, 16:00');
            $table->integer('daily_quota')->default(5);
            $table->string('description', 255)->nullable();
            $table->string('icon', 20)->default('🩺');
            $table->string('card_color', 30)->default('card-green');
            $table->decimal('price', 12, 2);
            $table->timestamps();
        });

        Schema::create('health_booking', function (Blueprint $table) {
            $table->increments('booking_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('pet_id')->nullable();
            $table->unsignedInteger('service_id');
            $table->string('owner_name', 100);
            $table->string('pet_name', 100);
            $table->string('pet_species', 50);
            $table->dateTime('booking_date');
            $table->enum('status', ['pending', 'approved', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user')->cascadeOnDelete();
            $table->foreign('pet_id')->references('pet_id')->on('pet')->nullOnDelete();
            $table->foreign('service_id')->references('service_id')->on('health_service')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_booking');
        Schema::dropIfExists('health_service');
        Schema::dropIfExists('payment');
        Schema::dropIfExists('order_detail');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product');
        Schema::dropIfExists('adoption_request');
        Schema::dropIfExists('animal');
        Schema::dropIfExists('pet');
        Schema::dropIfExists('user');
    }
};
