<?php

namespace App\Models\Ypi;

use App\Models\Concerns\BelongsToEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    use HasFactory;
    use BelongsToEvent;
    protected $guarded = [];
    protected $table = 'nationalities';
}
