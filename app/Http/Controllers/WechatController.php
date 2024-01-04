<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WechatController extends Controller
{
    public function serve(Request $request)
    {
        $app = app('wechat.official_account');
        $server = $app->getServer();

        $server->addEventListener('subscribe', function($message, \Closure $next) {
            return '感谢您关注 一笔记账';
        });

        $server->addMessageListener('text', function($message, \Closure $next) {
            return '这是文本消息';
        });

        return $server->serve();
    }
}
