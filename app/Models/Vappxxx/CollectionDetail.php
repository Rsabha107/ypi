<?php

namespace App\Models\Vapp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionDetail extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'collection_details';

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

}
