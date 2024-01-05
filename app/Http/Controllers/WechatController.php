<?php

namespace App\Http\Controllers;

use App\Events\OfficialAccountMessageReceived;
use App\Events\OfficialAccountSubscribed;
use Illuminate\Http\Request;

class WechatController extends Controller
{
    public function serve(Request $request)
    {
        $server = app('easywechat.official_account')->getServer();

        $server->addEventListener('subscribe', function($message, \Closure $next) {
            event(new OfficialAccountSubscribed($message));

            return '感谢您关注 一笔记账，您可以发送任意消息来开启您的第一次记账，例如：买奶茶花了15';
        });

        $server->addMessageListener('text', function($message, \Closure $next) {
            event(new OfficialAccountMessageReceived($message));

            return $next($message);
        });

        return $server->serve();
    }
}
