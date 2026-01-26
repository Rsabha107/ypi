<?php

namespace App\Models\Ypi;

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
