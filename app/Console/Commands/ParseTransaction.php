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
                        'name' => 'get_category_and_amount',
                        'description' => 'Get the categories and amounts included in the description',
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
                            ],
                            'required' => ['category', 'amount'],
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
