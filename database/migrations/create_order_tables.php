<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 订单模块
        /*
         * 一个订单多个子项目，每个项目可以有不同的支付方式，不同的物流
         * 订单的金额会收到各种影响
         * 单项目子单位退款金额计算
         * 可拆分为子项目为子订单，这里需要灵活拆分，（如果可以，灵活组合）。还得加上拆分记录
         */

        $user = null;

        // 创建订单
        $order = $user->makeOrder()->setAmount()->save();
        $order = $user->orders()->create([]);

        $order->items()->create([]);

        // 订单：
        // 下单信息
        // 收货信息
        // 物流信息|是否可以分离
        // 商品列表
        // 共计金额
        // 折扣
        // 退款

        // 如果有货币类型区分，在外部多多对关联订单来实现
        // 收货地址也由外部提供，比较部分订单那没有地址也可以创建,根据产品情况

        // 订单创建后，订单内的子项目或者商品应该是不允许修改的，只允许修改收货信息或者取消订单

        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index()->comment('所属用户');
            $table->unsignedBigInteger('parent_id')->nullable()->index()->comment('父订单');
            $table->string('number')->index()->comment('订单号');

            $table->decimal('items_price_total')->default(0)->comment('order每一个item的total的和 unit/分');
            $table->decimal('adjustments_price_total')->default(0)->comment('调整金额 unit/分');
            $table->decimal('price')->default(0)->comment('需支付金额 unit/分');

            $table->string('state')->comment('主状态 checkout/new/cancelled/fulfilled');
            $table->string('payment_state')->comment('支付状态 checkout/awaiting_payment/partially_paid/cancelled/paid/partially_refunded/refunded');
            $table->string('shipment_state')->comment('运输状态 checkout/ready/cancelled/partially_shipped/shipped');

            // 时间点
            $table->timestamp('paid_at')->nullable()->comment('支付时间');
            $table->timestamp('confirmed_at')->nullable()->comment('确认订单时间');
            $table->timestamp('fulfilled_at')->nullable()->comment('订单完成时间');
            $table->timestamps();   // 下单时间，更新时间
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('order_id');
            $table->morphs('producible');
            $table->json('producible_origin')->comment('商品快照');
            $table->unsignedInteger('quantity')->comment('购买数量');

            // adjustment calculate
            $table->integer('units_total')->default(0)->comment('item中每一个unit的和. 单位/分');
            $table->integer('adjustments_total')->default(0);
            $table->integer('total')->default(0)->comment('units_total + adjustments_total');
            $table->integer('unit_price')->default(0)->comment('variant单价,冗余字段');

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::create('order_item_units', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id')->index();
            $table->unsignedInteger('shipment_id');
            $table->integer('adjustments_total')->default(0);
            $table->timestamps();
        });

        Schema::create('adjustments', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('order_item_id')->nullable();
            $table->unsignedInteger('order_item_unit_id')->nullable();

            $table->string('type')->comment('调整的类型 shipping/promotion/tax等等');

            $table->string('label')->comment('结合type决定');

            $table->string('origin_code')->comment('结合label决定');

            $table->bool('included')->comment('是否会影响最终订单需要支付的价格');
            $table->integer('amount');
            $table->timestamps();

            $table->index('order_id');
            $table->index('order_item_id');
            $table->index('order_item_unit_id');
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('method_id')->comment('运输方式 外键');
            $table->unsignedInteger('order_id')->comment('订单 外键');
            $table->string('state')->comment('运输状态');
            $table->string('tracking_number')->nullable()->comment('订单号码');
            $table->timestamps();

            $table->index('order_id');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('method_id')->comment('支付方式');
            $table->unsignedInteger('order_id');
            $table->string('currency_code', 3)->comment('冗余 货币编码');
            $table->unsignedInteger('amount')->default(0)->comment('支付金额');
            $table->string('state');
            $table->text('details')->nullable();
            $table->timestamps();

            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
