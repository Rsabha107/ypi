<?php

namespace App\Models\Gms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostedBy extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'hosted_by';
}
