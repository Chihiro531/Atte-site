<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_record_id', 'time_in_out'];

    protected $casts = [
        'time_in_out' => 'array',
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->time_in_out = [];
    }

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function getTimeIn()
    {
        return isset($this->time_in_out['time_in']) ? Carbon::parse($this->time_in_out['time_in'])->format('H:i:s') : null;
    }

    public function getTimeOut()
    {
        return isset($this->time_in_out['time_out']) ? Carbon::parse($this->time_in_out['time_out'])->format('H:i:s') : null;
    }
    public function setTimeIn($time)
{
    $this->setAttribute('time_in_out', array_merge($this->getAttribute('time_in_out'), ['time_in' => $time]));
    $this->save();
}

public function setTimeOut($time)
{
    $this->setAttribute('time_in_out', array_merge($this->getAttribute('time_in_out'), ['time_out' => $time]));
    $this->save();
}
}







