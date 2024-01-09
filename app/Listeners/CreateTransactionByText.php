<?php

namespace App\Listeners;

use App\Events\OfficialAccountMessageReceived;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateTransactionByText implements ShouldQueue
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
    public function handle(OfficialAccountMessageReceived $event): void
    {
        $message = $event->getMessage();

        $openid = $message->FromUserName;
        $content = $message->Content;
        $user = User::query()->where('weixin_openid', $openid)->first();
        $ledger = $user->getDefaultLedger();
        $categories = $ledger->categories;

        $now = now()->format('r');
        $response = app('openai')->chat()->create([
            'model' => 'gpt-4-1106-preview',
            'messages' => [
                ['role' => 'system', 'content' => "Current date is: {$now}"],
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
                                'date' => [
                                    'type' => 'string',
                                    'description' => '日期，格式为：2023-01-01',
                                ],
                            ],
                            'required' => ['category', 'type', 'amount'],
                        ],
                    ],
                ]
            ]
        ]);

        if ($response->choices && $toolCalls = $response->choices[0]->message->toolCalls) {
            $data = [];
            foreach ($toolCalls as $toolCall) {
                $data = array_merge($data, json_decode($toolCall->function->arguments, true) ?? []);
            }

            if (!isset($data['amount'])) {
                $this->sendTextMessage($openid, '记账失败：未识别到金额，请重新描述');
                return;
            }

            $amount = $data['amount'];
            $type = array_flip(Transaction::$typeMap)[$data['type']] ?? Transaction::TYPE_EXPENSE;
            $transaction = new Transaction([
                'amount' => $amount,
                'type' => $type,
                'transacted_at' => isset($data['date']) ? Carbon::make($data['date']) : now(),
                'remark' => $content
            ]);

            $categoryName = $data['category'] ?? '';
            $category = $categories->where('name', $categoryName)->first();
            $transaction->category()->associate($category);
            $transaction->ledger()->associate($ledger);
            $transaction->save();

            $text = join(" ", array_filter([$categoryName, Transaction::$typeMap[$type], "{$amount}元"]));
            $this->sendTextMessage($openid, $text);
        }
    }

    protected function sendTextMessage($openid, $message)
    {
        $client = app('easywechat.official_account')->getClient();

        return $client->postJson('/cgi-bin/message/custom/send', [
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => [
                'content' => $message
            ]
        ]);
    }
}
