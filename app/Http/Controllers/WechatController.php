<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WechatController extends Controller
{
    public function serve(Request $request)
    {
        $server = app('easywechat.official_account')->getServer();

        $server->addEventListener('subscribe', function($message, \Closure $next) {
            $openid = $message->FromUserName;
            User::query()->firstOrCreate([
                'weixin_openid' => $openid
            ], [
                'name' => '新用户' . Str::random(6)
            ]);

            return '感谢您关注 一笔记账
            请输入类似“买奶茶花了15”的话，开启您的第一次记账';
        });

        $server->addEventListener('text', function($message, \Closure $next) {


            return '感谢您关注 EasyWeChat!';
        });

        return $server->serve();
    }
}
