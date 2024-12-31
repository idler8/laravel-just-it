<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** 
 * 用户邮件
 */
class Post extends Model
{
    use HasFactory;
    protected $table = 'post';
    protected $guarded = ['id'];
}
