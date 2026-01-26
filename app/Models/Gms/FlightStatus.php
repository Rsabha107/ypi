<?php

namespace App\Models\Gms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightStatus extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'flight_statuses';

     public $timestamps = false; // 🔹 prevent Eloquent from looking for created_at/updated_at
}
