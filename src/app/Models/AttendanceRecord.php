<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'user_name',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakLogs()
    {
        return $this->hasMany(BreakLog::class);
    }
    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }
    public function calculateWorkTime()
    {
        // 当該日の休憩時間を取得
        $totalBreakTime = $this->calculateTotalBreakTime();

        // 勤怠の時間ログを取得
        $timeLogs = $this->timeLogs;

        // 勤務時間の合計を初期化
        $totalWorkTime = 0;

        foreach ($timeLogs as $timeLog) {
            $timeIn = isset($timeLog->time_in_out['time_in']) ? Carbon::parse($timeLog->time_in_out['time_in']) : null;
            $timeOut = isset($timeLog->time_in_out['time_out']) ? Carbon::parse($timeLog->time_in_out['time_out']) : null;

            if ($timeIn && $timeOut) {
                // 勤務時間を秒単位で計算
                $workTime = $timeOut->diffInSeconds($timeIn);

                // 休憩時間を差し引く
                $workTime -= $totalBreakTime;

                // 負の値にならないように調整
                $workTime = max($workTime, 0);

                // 合計勤務時間に加算
                $totalWorkTime += $workTime;
            }
        }

        // 秒単位の勤務時間を時、分、秒に変換して返す
        return gmdate("H:i:s", $totalWorkTime);
    }

    private function calculateTotalBreakTime()
    {
        // 勤怠記録に紐づく休憩ログを取得
        $breakLogs = $this->breakLogs;

        // 休憩時間の合計を初期化
        $totalBreakTime = 0;

        foreach ($breakLogs as $breakLog) {
            $breakIn = isset($breakLog->break_in) ? Carbon::parse($breakLog->break_in) : null;
            $breakOut = isset($breakLog->break_out) ? Carbon::parse($breakLog->break_out) : null;

            if ($breakIn && $breakOut) {
                // 休憩時間を秒単位で計算
                $breakTime = $breakOut->diffInSeconds($breakIn);

                // 合計休憩時間に加算
                $totalBreakTime += $breakTime;
            }
        }

        return $totalBreakTime;
    }
}

