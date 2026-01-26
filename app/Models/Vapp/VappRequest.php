<?php

namespace App\Models\Vapp;

use App\Models\Vapp\RequestNumGen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable;

class VappRequest extends Model  implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    protected $table = 'vapp_requests';

    public static function boot()
    {

        parent::boot();

        // static::addGlobalScope(new NonArchivedScope);

        static::creating(function ($model) {
            appLog('Creating VappRequest model', ['model' => $model]);
            $generated_number = RequestNumGen::firstOrFail();
            $last_number = $generated_number->max('last_number') + 1;
            $generated_number->update(['last_number' => $last_number]);

            $model->request_number = 'VAPP' . '-' . str_pad($last_number, 5, '0', STR_PAD_LEFT);
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function match()
    {
        return $this->belongsTo(MatchList::class, 'match_id');
    }

    public function match_category()
    {
        return $this->belongsTo(MatchCategory::class, 'match_category_id');
    }

    public function functional_area()
    {
        return $this->belongsTo(FunctionalArea::class, 'vapp_functional_area_id');
    }

    public function parking()
    {
        return $this->belongsTo(ParkingMaster::class, 'parking_id');
    }

    public function vapp_size()
    {
        return $this->belongsTo(VappSize::class, 'vapp_size_id');
    }
    public function variation()
    {
        return $this->belongsTo(VappVariation::class, 'variation_id');
    }

    public function status()
    {
        return $this->belongsTo(VappRequestStatus::class, 'request_status_id');
    }
    public function user_created_by()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
    public function user_updated_by()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
