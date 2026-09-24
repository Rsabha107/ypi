<?php

namespace App\Models\Ypi;

use App\Models\Concerns\BelongsToEvent;
use Illuminate\Database\Eloquent\Model;

class SizeLookup extends Model
{
    use BelongsToEvent;

    protected $fillable = [
        'event_id', 'type', 'code', 'label', 'gender', 'sort_order', 'active'
    ];

    public function scopeType($query, $type)
    {
        return $query->where('type', $type)->where('active', 1)->orderBy('sort_order');
    }
}
