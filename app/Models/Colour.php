<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Colour extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'hex',
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
