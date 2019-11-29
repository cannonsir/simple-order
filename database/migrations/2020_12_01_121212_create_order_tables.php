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

//        $user = null;

        // 创建订单
//        $order = $user->makeOrder()->setAmount()->save();
//        $order = $user->orders()->create([]);
//
//        $order->items()->create([]);

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

        $tableNames = config('simple-order.table_names');
        $precision = config('simple-order.decimal_precision');

        // 订单表
        Schema::create($tableNames['orders'], function (Blueprint $table) use ($precision) {
            $table->bigIncrements('id');
//            $table->unsignedBigInteger('user_id')->index()->comment('所属用户');

            // 有父订单的概念，有时候一个订单下单后被拆分成多个订单。其实就是创建了n+1个订单，第一个订单为父订单，子订单部分字段继承父订单的值
            // 但是拆分的时候不是根据订单来拆分的，是根据父订单的子项目部分字段来拆分的
            // 当有父订单时，将以子订单的状态为主，父订单状态无效
            // 但是无限极的话就简单粗暴了
            $table->boolean('is_parent')->default(false)->comment('是否为父订单 部分字段将失效');
            $table->unsignedBigInteger('parent_id')->nullable()->index()->comment('父订单ID');
            $table->string('number')->index()->comment('订单号');

            $table->string('state')->nullable()->comment('主状态 checkout/new/cancelled/fulfilled');

            // 时间点
            $table->timestamp('confirmed_at')->nullable()->comment('确认订单时间');
            $table->timestamp('fulfilled_at')->nullable()->comment('订单完成时间');
            $table->timestamps();   // 下单时间，更新时间
        });

        // 订单项目表，同一订单下可能有多个子商品
        Schema::create($tableNames['order_items'], function (Blueprint $table) use ($tableNames, $precision) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index();
            // 商品id，商品模型使用trait,可能时商品，可能是SKU
            $table->morphs('orderable');
            $table->text('orderable_serialize')->comment('商品快照');
            $table->unsignedInteger('quantity')->comment('购买数量');

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on($tableNames['orders'])->onDelete('cascade');
        });

        // 订单商品的最小单位，精确到每一个数量,每个单位提供不同的物流，支付，等灵活操作
        Schema::create($tableNames['order_item_units'], function (Blueprint $table) use ($tableNames) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id')->index();

            $table->timestamps();

            $table->foreign('item_id')->references('id')->on($tableNames['order_items'])->onDelete('cascade');
        });

        // 订单金额最终计算的时候，由最小单位开始算到订单，退款时可精确到每一个小单位，退款对应单位的金额

        // 金额表
        Schema::create($tableNames['amounts'], function (Blueprint $table) use ($precision, $tableNames) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->nullable()->unique()->index();
            $table->unsignedBigInteger('order_item_id')->nullable()->unique()->index();
            $table->unsignedBigInteger('order_item_unit_id')->nullable()->unique()->index();

            // 调整金额
            $table->decimal('adjustments_amount_total', $precision['total'], $precision['places'])->default(0)->comment('调整总金额 冗余');
            // 原本金额
            $table->decimal('should_amount', $precision['total'], $precision['places'])->default(0)->comment('原始金额');
            // 调整后金额
            $table->decimal('res_amount', $precision['total'], $precision['places'])->default(0)->comment('调整后金额');

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on($tableNames['orders'])->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on($tableNames['order_items'])->onDelete('cascade');
            $table->foreign('order_item_unit_id')->references('id')->on($tableNames['order_item_units'])->onDelete('cascade');
        });

        // 金额调整表，影响订单价格 是否必须
        // 订单可能有运费支出，最小单位unit可能有优惠券优惠，此表精确记录每个单位的应付金额，方便后续退款
        Schema::create($tableNames['adjustments'], function (Blueprint $table) use ($precision, $tableNames) {
            $table->increments('id');

            $table->unsignedBigInteger('amount_id')->index()->comment('所属金额记录');

            // 调整类型,优惠券，运费等
            $table->nullableMorphs('adjustable');
            $table->string('label')->nullable()->comment('标注');

            // 例如商品税通常包含在商品价格中，无需支付却需要展示清楚
            $table->boolean('included')->default(true)->comment('是否会影响最终订单需要支付的价格');
            $table->decimal('amount', $precision['total'], $precision['places'])->default(0)->comment('金额');

            $table->timestamps();

            $table->foreign('amount_id')->references('id')->on($tableNames['amounts'])->onDelete('cascade');
        });


        /**
         * ======================
         * 物流表和支付表暂时不涉及
         * ======================
         */
        // 物流信息表 是否必须
        false && Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('method_id')->comment('运输方式 外键');
            $table->unsignedInteger('order_id')->index()->comment('订单 外键');
            $table->string('state')->comment('运输状态');
            $table->string('tracking_number')->nullable()->comment('订单号码');
            $table->timestamps();
        });

        // 支付信息表 是否必须
        // 假设可通过多种支付方式支付，那么退款时如何计算金额退至哪儿？
        false && Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('method_id')->comment('支付方式');
            $table->unsignedInteger('order_id')->unique();
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
        $tableNames = config('simple-order.table_names');

        foreach ($tableNames as $table) {
            Schema::dropIfExists($table);
        }
    }
}
