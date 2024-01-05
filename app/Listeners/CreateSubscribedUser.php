<?php

namespace App\Listeners;

use App\Events\OfficialAccountSubscribed;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class CreateSubscribedUser implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OfficialAccountSubscribed $event): void
    {
        $message = $event->getMessage();

        // 创建用户
        User::firstOrCreate([
            'weixin_openid' => $message->FromUserName
        ], [
            'name' => '新用户' . Str::random(6)
        ]);
    }
}
