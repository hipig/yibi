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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->comment('账本');
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->string('name')->comment('名称');
            $table->string('description', 1024)->nullable()->comment('描述');
            $table->boolean('is_default')->comment('是否默认');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
