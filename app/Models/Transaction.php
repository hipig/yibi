<?php

namespace App\Models;


class Transaction extends Model
{
    const TYPE_INCOME = 'INCOME';
    const TYPE_EXPENSE = 'EXPENSE';

    public static $typeMap = [
        self::TYPE_INCOME => '收入',
        self::TYPE_EXPENSE => '支出'
    ];

    protected $fillable = [
        'amount',
        'type',
        'transacted_at',
        'remark'
    ];

    protected $casts = [
        'transacted_at' => 'datetime',
    ];

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


}
