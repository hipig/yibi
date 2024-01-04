<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WechatController extends Controller
{
    public function serve(Request $request)
    {
        $app = app('wechat.official_account');
        $server = $app->getServer();

        $server->with(function($message, \Closure $next) {
            switch ($message->MsgType) {
                case 'event':
                    if ($message->Event === 'subscribe') {
                        return '感谢您关注 一笔记账';
                    }
                    break;
                case 'text':
                    return '收到文本消息';
                default:
                    return '收到其它消息';
            }

            return $next($message);
        });

        return $server->serve();
    }
}
