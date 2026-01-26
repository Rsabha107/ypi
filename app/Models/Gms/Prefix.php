<?php

namespace App\Models\Gms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Prefix extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'prefixes';
    protected $primaryKey = 'id';


}
