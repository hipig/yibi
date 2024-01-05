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
        Schema::create('categories', function (Blueprint $table) {
            $table->comment('分类');
            $table->id();
            $table->unsignedBigInteger('ledger_id')->comment('账本ID');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('上级ID');
            $table->string('name')->comment('名称');
            $table->boolean('is_directory')->comment('是否拥有子类目');
            $table->unsignedInteger('level')->default(0)->comment('层级');
            $table->string('path')->comment('父类目路径');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
