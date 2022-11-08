<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserValoper extends Model
{
    use HasFactory;

    protected $fillable = [
        'chatId',
        'valoper'
    ];
}
