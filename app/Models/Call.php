<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'call_id',
        'caller_id',
        'receiver_id',
        'status',
        'started_at',
        'ended_at'
    ];
}