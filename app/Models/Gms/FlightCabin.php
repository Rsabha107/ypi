<?php

namespace App\Models\Gms;

use App\Models\Designation;
use App\Models\Event;
use App\Models\Nationality;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightCabin extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'cabin_types';

    public $timestamps = false; // <-- Add this

}
