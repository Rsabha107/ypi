<?php

namespace App\Models\Gms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'nationalities';
}
