<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class tasks extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'app_id',
        'carrier',
        'type',
        'source',
        'assigned',
        'desc',
        'status',
        'notes',
        'fixed'
    ];
}
