<?php

namespace App\Models\Ypi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'allergens';
}
