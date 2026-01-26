<?php

namespace App\Models\Gms;

use App\Models\Designation;
use App\Models\Event;
use App\Models\Nationality;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestAccommodation extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'accommodations';

    public function property()
    {
        return $this->belongsTo(PropertyType::class, 'property_id');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }


    // public function airport()
    // {
    //     return $this->belongsTo(Airport::class, 'airport_id');
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}
