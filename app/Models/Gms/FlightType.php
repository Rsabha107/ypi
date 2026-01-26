<?php

namespace App\Models\Gms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightType extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'flight_types';

    public $timestamps = false; // <-- Add this

}
