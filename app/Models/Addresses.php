<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{
    use HasFactory;

    protected $table = 'addresses';
    protected $fillable = [
        'name',
        'postal_code',
        'city',
        'public_space',
        'house_number'
    ];
}
