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
        Schema::create('transactions', function (Blueprint $table) {
            $table->comment('账单');
            $table->id();
            $table->unsignedBigInteger('ledger_id')->comment('账本ID');
            $table->unsignedBigInteger('category_id')->nullable()->comment('分类ID');
            $table->decimal('amount', 12)->default(0)->comment('金额');
            $table->string('type', 64)->default(\App\Models\Transaction::TYPE_EXPENSE)->comment('类型');
            $table->timestamp('transacted_at')->nullable()->comment('交易时间');
            $table->text('remark')->nullable()->comment('备注');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
