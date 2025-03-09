<?php

namespace JulianAnturi\laraStarted\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'statusCode',
        'logDetallesMisionId',
        'filename',
        'app',
       'line'
    ];
}

