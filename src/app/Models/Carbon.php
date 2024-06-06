<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class YourModel extends Model
{
    use HasFactory;

    public function getTimeInAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getTimeOutAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }
}
