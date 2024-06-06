<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BreakLog extends Model
{
    protected $fillable = [
        'attendance_record_id',
        'break_in',
        'break_out',
    ];

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class);
    }
    public static function getTotalBreakTime()
    {
        $breakLogs = self::all();

        $totalBreakSeconds = 0;

        foreach ($breakLogs as $breakLog) {
            if (!empty($breakLog->break_in) && !empty($breakLog->break_out)) {
                $breakIn = Carbon::parse($breakLog->break_in);
                $breakOut = Carbon::parse($breakLog->break_out);
                $breakTime = $breakOut->diffInSeconds($breakIn);
                $totalBreakSeconds += $breakTime;
            }
        }

        $totalBreakHours = floor($totalBreakSeconds / 3600);
        $totalBreakMinutes = floor(($totalBreakSeconds % 3600) / 60);
        $totalBreakSeconds = $totalBreakSeconds % 60;

        return [
            'hours' => $totalBreakHours,
            'minutes' => $totalBreakMinutes,
            'seconds' => $totalBreakSeconds,
        ];
    }
}
