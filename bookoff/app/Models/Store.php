<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table='store';
    protected $fillable = [
        'user_id',
        'store_id',
        'store_login_id',
        'store_login_pwd',
        'qoo10_api_key',
        'qoo10_auth_key',
        'price_multiple'
       
    ];

}
