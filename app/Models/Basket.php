<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    protected $fillable = [
        'user_id',
        'items'
    ];

    protected $casts = [
        'items' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
