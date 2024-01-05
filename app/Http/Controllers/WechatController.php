<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WechatController extends Controller
{
    public function serve(Request $request)
    {
        Log::info('message received：', $request->all());
        $server = app('easywechat.official_account')->getServer();

        $server->with(function($message){
            return "欢迎关注 一笔记账哈哈哈哈！";
        });

        return $server->serve();
    }
}
