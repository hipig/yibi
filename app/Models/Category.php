<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;

class Category extends Model
{

    protected $fillable = [
        'name',
        'is_directory',
        'level',
        'path'
    ];

    protected $casts = [
        'is_directory' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听 Category 的创建事件，用于初始化 path 和 level 字段值
        static::creating(function (Category $category) {
            // 如果创建的是一个根类目
            if (is_null($category->parent_id)) {
                // 将层级设为 0
                $category->level = 0;
                // 将 path 设为 -
                $category->path  = '-';
            } else {
                // 将层级设为父类目的层级 + 1
                $category->level = $category->parent->level + 1;
                // 将 path 值设为父类目的 path 追加父类目 ID 以及最后跟上一个 - 分隔符
                $category->path  = $category->parent->path . $category->parent_id . '-';
            }
        });
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // 定义一个访问器，获取所有祖先类目的 ID 值
    public function pathIds() :Attribute
    {
        return Attribute::get(
            fn() => array_filter(explode('-', trim($this->path, '-')))
        );
    }

    // 定义一个访问器，获取所有祖先类目并按层级排序
    public function ancestors() :Attribute
    {
        return Attribute::get(
            fn() => Category::query()->whereIn('id', $this->path_ids)->orderBy('level')->get()
        );
    }

    // 定义一个访问器，获取以 - 为分隔的所有祖先类目名称以及当前类目的名称
    public function fullName() :Attribute
    {
        return Attribute::get(
            fn() => $this->ancestors->pluck('name')->push($this->name)->implode(' - ')
        );
    }
}
