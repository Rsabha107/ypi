<?php

namespace App\Models\Ypi;

use App\Models\Concerns\BelongsToEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantType extends Model
{
    //
    use HasFactory;
    use BelongsToEvent;
    protected $guarded = [];
    protected $table = 'participant_types';
}
