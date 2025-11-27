<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade'); // どの商品の取引か
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // 送信者
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade'); // 受信者
            $table->text('message')->nullable(); // メッセージ内容
            $table->string('image_path')->nullable(); // 画像があれば
            $table->boolean('is_read')->default(false); // 既読フラグ（通知数カウント用）
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
        Schema::dropIfExists('transaction_messages');
    }
}