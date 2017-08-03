<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralRentInformationCollectionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_rent_information_collections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city', 30)->nullable()->comment('城市');
            $table->string('block', 30)->nullable()->comment('商圈');
            $table->unsignedInteger('xiaoqu_id')->nullable()->index()->comment('laputa库xiaoqus表id');
            $table->string('xiaoqu_name', 30)->nullable()->comment('小区名');
            $table->string('lng')->nullable()->comment('小区精度');
            $table->string('lat')->nullable()->comment('小区纬度');
            $table->string('building_code', 30)->nullable()->comment('楼号');
            $table->string('floor', 30)->nullable()->comment('楼层:爬楼5层及以上:L1,爬楼1层：L2,正常楼层:L3');
            $table->unsignedInteger('area')->nullable()->comment('建筑面积');
            $table->unsignedInteger('bedroom_num')->nullable()->comment('原始卧室数');
            $table->unsignedInteger('bef_gw')->nullable()->comment('原始公卫数');
            $table->unsignedInteger('bef_dw')->nullable()->comment('原始独卫数');
            $table->string('room_status', 30)->nullable()->comment('原始装修水平:精装R1，简装R2，老旧/毛坯R2');
            $table->string('enviorment_level', 30)->nullable()->comment('环境情况:安静卫生N1，吵闹脏乱N2');
            $table->unsignedInteger('sale_price')->nullable()->comment('普租价');
            $table->unsignedInteger('check_price')->nullable()->comment('审核修改后普租价');
            $table->string('source', 30)->nullable()->comment('价格来源渠道:个人评估/渠道获取/链家网/自如网/我爱我家网/58赶集网/家家顺/豪世华邦网/其它网站');
            $table->text('picture')->nullable()->comment('价格来源照片（非必填）');
            $table->unsignedInteger('user_id')->nullable()->index()->comment('录入人');
            $table->dateTime('create_time')->nullable()->comment('录入时间');
            $table->unsignedInteger('checker_id')->nullable()->index()->comment('审批人');
            $table->string('check_status', 30)->default('待审核')->comment('审批状态:待审核/已入库/作废');
            $table->text('check_note')->nullable()->comment('审核备注');
            $table->dateTime('check_time')->nullable()->comment('审批时间');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_rent_information_collections');
    }
}
