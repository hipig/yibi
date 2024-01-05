<?php

namespace App\Models;


class Ledger extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function (Ledger $ledger) {
            // 创建默认账本分类
            $categoryNames = ['餐饮日常', '居家生活', '购物消费', '零食水果', '出行交通', '休闲娱乐', '文化教育', '礼物红包', '其他'];
            foreach ($categoryNames as $name) {
                $category = new Category([
                    'name' => $name,
                ]);
                $category->ledger()->associate($ledger);
                $category->save();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'ledger_id');
    }

    public function setDefault()
    {
        $this->is_default = true;
        $this->save();
    }
}
