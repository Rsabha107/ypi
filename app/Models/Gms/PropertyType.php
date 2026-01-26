<?php

namespace App\Models\Gms;

use App\Models\Designation;
use App\Models\Event;
use App\Models\Nationality;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyType extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'property_types';
}
