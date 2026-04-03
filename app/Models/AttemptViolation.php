<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttemptViolation extends Model
{
    protected $fillable = ['attempt_id', 'type', 'data'];
}
