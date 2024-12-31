<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** 
 * 用户账户
 */
class Account extends Model
{
    use HasFactory;
    protected $table = 'account';
    protected $guarded = ['id'];
    protected $hidden = ['password'];
    protected $casts = ['password' => 'hashed'];
    public function scopeHalf_middle($query, $value)
    {
        $count = Account::count();
        return $query->skip(floor($count / 4))->take(ceil($count / 2));
    }
    public function posts()
    {
        return $this->hasMany(Post::class, 'account_id', 'id');
    }
}
