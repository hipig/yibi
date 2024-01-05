<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ParseTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:parse {text}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse transaction';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $text = $this->argument('text');

        $response = app('openai')->chat()->create([
            'model' => 'gpt-4-1106-preview',
            'messages' => [
                ['role' => 'user', 'content' => $text],
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
                                    'enum' => ['餐饮日常', '居家日常', '购物消费', '零食水果', '其他'],
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

        foreach ($response->choices as $result) {
            dump($result->message);
        }
    }
}
