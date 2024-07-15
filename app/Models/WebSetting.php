<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_share_link',
        'telegram_chat_link',
        'refer_bonus',
        'level_percentage'
    ];

  
}
