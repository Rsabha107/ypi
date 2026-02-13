<?php

namespace App\Models\Ypi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventMatch extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'matches';
    protected $casts = [
        'match_date' => 'datetime',
    ];
}
