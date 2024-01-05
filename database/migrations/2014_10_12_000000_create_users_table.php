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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('昵称');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('phone')->nullable()->comment('手机号码');
            $table->string('email')->nullable()->comment('邮箱地址');
            $table->timestamp('phone_verified_at')->nullable()->comment('手机号码验证时间');
            $table->timestamp('email_verified_at')->nullable()->comment('邮箱验证时间');
            $table->string('password')->nullable()->comment('密码');
            $table->string('weixin_openid')->nullable()->unique()->comment('微信openid');
            $table->string('weixin_unionid')->nullable()->unique()->comment('微信unionid');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
