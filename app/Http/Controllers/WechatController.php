<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
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
            User::firstOrCreate([
                'weixin_openid' => $message->FromUserName
            ], [
                'name' => '新用户' . Str::random(6)
            ]);

            return '感谢您关注 一笔记账
            请输入类似“买奶茶花了15”的话，开启您的第一次记账';
        });

        $server->addMessageListener('text', function($message, \Closure $next) {
            $content = $message->Content;
            $user = User::query()->where('weixin_openid', $message->FromUserName)->first();
            $ledger = $user->getDefaultLedger();
            $categories = $ledger->categories;

            $response = app('openai')->chat()->create([
                'model' => 'gpt-4-1106-preview',
                'messages' => [
                    ['role' => 'user', 'content' => $content],
                ],
                'tools' => [
                    [
                        'type' => 'function',
                        'function' => [
                            'name' => 'format_transaction_text',
                            'description' => '解析文本账单',
                            'parameters' => [
                                'type' => 'object',
                                'properties' => [
                                    'category' => [
                                        'type' => 'string',
                                        'description' => '分类',
                                        'enum' => $categories->pluck('name')->toArray(),
                                    ],
                                    'amount' => [
                                        'type' => 'number',
                                        'description' => '金额',
                                    ],
                                    'type' => [
                                        'type' => 'string',
                                        'description' => '交易类型',
                                        'enum' => ['支出', '收入'],
                                    ],
                                ],
                                'required' => ['category', 'type', 'amount'],
                            ],
                        ],
                    ]
                ]
            ]);

            if ($response->choices && $toolCalls = $response->choices[0]->toolCalls) {
                $data = [];
                foreach ($toolCalls as $toolCall) {
                    $data = array_merge($data, json_decode($toolCall->function->arguments, true));
                }

                $amount = $data['amount'];
                $type = array_flip(Transaction::$typeMap)[$data['type']] ?? Transaction::TYPE_EXPENSE;
                $transaction = new Transaction([
                    'amount' => $amount,
                    'type' => $type,
                    'transacted_at' => now(),
                    'remark' => $content
                ]);

                $categoryName = $data['category'] ?? '';
                $category = $categories->where('name', $categoryName)->first();
                $transaction->category()->associate($category);

                $transaction->save();

                return join(" ", array_filter([$categoryName, Transaction::$typeMap[$type], "{$amount}元"]));
            }

            return '账单解析失败，请重新描述或手动记账';
        });

        return $server->serve();
    }
}
