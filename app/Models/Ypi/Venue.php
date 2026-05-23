<?php

namespace App\Models\Ypi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'venues';

    public function matches()
    {
        return $this->hasMany(EventMatch::class, 'venue_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'venue_event', 'venue_id', 'event_id');
    }
}
