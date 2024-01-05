<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'phone',
        'email',
        'password',
        'weixin_openid',
        'weixin_unionid'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function (User $user) {
            // 创建默认账本
            $ledger = new Ledger([
                'name' => '日常账本'
            ]);
            $ledger->user()->associate($user);
            $ledger->save();
            $ledger->setDefault();
        });
    }

    public function ledgers()
    {
        return $this->hasMany(Ledger::class, 'user_id');
    }

    public function getDefaultLedger()
    {
        return $this->ledgers()->where('is_default', true)->first();
    }
}
