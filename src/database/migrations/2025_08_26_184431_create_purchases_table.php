<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 購入者
            $table->foreignId('item_id')->constrained()->onDelete('cascade'); // 購入商品
            $table->string('payment_method'); // 支払い方法（コンビニ支払い、カード支払い）
            $table->integer('amount'); // 決済金額
            $table->string('status')->default('pending'); // 決済ステータス
            $table->timestamp('purchased_at'); // 購入日時
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
        Schema::dropIfExists('purchases');
    }
}