<?php

namespace App\Models\Ypi;

use App\Models\Designation;
use App\Models\Event;
use App\Models\Nationality;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestFlight extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'guest_flights';

    public static function boot()
    {

        parent::boot();

        try {
            static::creating(function ($model) {
                $numGen = DeliveryNumGen::first();
                if ($numGen == null) {
                    $numGen = new DeliveryNumGen();
                    $numGen->last_number = 0;
                    $numGen->save();
                }
                $last_number = $numGen->max('last_number') + 1;
                $numGen->update(['last_number' => $last_number]);

                $model->ref_number = 'FLT' . '-' . str_pad($last_number, 5, '0', STR_PAD_LEFT);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $errorInfo = $e->errorInfo;
            return redirect()->back()->with('error', $errorInfo[2]);
            // dd($e->getMessage());
        }
    }

    public function guestName()
    {
        return $this->belongsTo(Guest::class, 'first_name');
    }
    public function airline()
    {
        return $this->belongsTo(AirlineCarriers::class, 'airline_id');
    }

    public function cabin()
    {
        return $this->belongsTo(FlightCabin::class, 'flight_cabin_id');
    }

    public function flight_type()
    {
        return $this->belongsTo(FlightType::class, 'flight_type_id');
    }

    // public function airport()
    // {
    //     return $this->belongsTo(Airport::class, 'airport_id');
    // }

    public function departureAirport()
    {
        return $this->belongsTo(Airport::class, 'departure_airport_id');
    }

    public function arrivalAirport()
    {
        return $this->belongsTo(Airport::class, 'arrival_airport_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function flightStatus()
    {
        return $this->belongsTo(FlightStatus::class, 'status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Inside GuestFlight model
      public function getFullFlightAttribute()
    {
        // Use optional() in case airline relationship is null
        return $this->flight_number . ', ' . optional($this->airline)->name;
    }

}
