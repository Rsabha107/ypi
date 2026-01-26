<?php

namespace App\Models\Gms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirlineCarriers extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'airline_carriers';
    public $timestamps = false; // 🔹 prevent Eloquent from looking for created_at/updated_at
}
