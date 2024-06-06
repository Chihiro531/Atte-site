<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Models\BreakLog;
use App\Models\TimeLog;
use App\Models\User;
use Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $loggedInUser = Auth::user();
        $attendanceRecords = AttendanceRecord::with('breakLogs', 'timeLogs', 'user')
                                             ->where('user_id', $loggedInUser->id)
                                             ->get();

        // 各出勤記録ごとに休憩時間と勤務時間を計算
        foreach ($attendanceRecords as $record) {
            $record->totalBreakTime = $this->calculateTotalBreakTime($record->breakLogs);
            $record->totalDutyTime = $this->calculateTotalDutyTime($record->timeLogs, $record->totalBreakTime);
        }

        $latestAttendanceRecord = AttendanceRecord::where('user_id', $loggedInUser->id)->latest()->first();

        $is_syukkin = true;
        if (!$latestAttendanceRecord || $latestAttendanceRecord->date !== now()->toDateString()) {
            $is_syukkin = false;
        }
        return view('attendance.index', compact('attendanceRecords', 'is_syukkin'));
    }

public function userList()
{
    $start_date = Carbon::now()->startOfMonth();
    $end_date = Carbon::now()->endOfMonth();

    $dates = $this->generateDateRange($start_date, $end_date);

    $attendanceRecords = AttendanceRecord::with('user')->get();

    $attendanceData = User::all()->map(function ($user) use ($attendanceRecords, $dates) {
        $recordsForUser = $attendanceRecords->where('user_id', $user->id);
        $attendance = [];
        foreach ($dates as $date) {
            $attendance[] = $recordsForUser->contains(function ($record) use ($date) {
                return $record->date === $date['full'];
            }) ? '⚪︎' : '-';
        }
        return ['user' => $user, 'attendance' => $attendance];
    });

    return view('userlist', compact('dates', 'attendanceData'));
}

private function generateDateRange($start_date, $end_date)
{
    $dates = [];
    for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
        $dates[] = [
            'full' => $date->format('Y-m-d'),
            'display' => $date->format('m月d日'),
        ];
    }
    return $dates;
}



    public function timeIn()
{
    $user = Auth::user();
    $latestAttendanceRecord = AttendanceRecord::where('user_id', $user->id)->latest()->first();

    if ($latestAttendanceRecord && $latestAttendanceRecord->date === now()->toDateString()) {
        // すでに今日の出勤記録がある場合は、エラーを返す
        return redirect()->back()->with('error', 'すでに出勤しています。');
    }

    // 新しい出勤記録を作成
    $attendanceRecord = new AttendanceRecord();
    $attendanceRecord->user_id = $user->id;
    $attendanceRecord->date = now()->toDateString();
    $attendanceRecord->save();

    // 出勤時間を記録
    $timeLog = new TimeLog();
    $timeLog->attendance_record_id = $attendanceRecord->id;
    $timeLog->setTimeIn(now()->toDateTimeString());
    $timeLog->save();

    return redirect()->back()->with('success', '出勤を記録しました。');
}

public function timeOut()
{
    $user = Auth::user();
    $latestAttendanceRecord = AttendanceRecord::where('user_id', $user->id)->latest()->first();

    if (!$latestAttendanceRecord || $latestAttendanceRecord->date !== now()->toDateString()) {
        // 今日の出勤記録がない場合は、エラーを返す
        return redirect()->back()->with('error', '出勤記録が見つかりませんでした。');
    }

    $timeLog = TimeLog::where('attendance_record_id', $latestAttendanceRecord->id)->latest()->first();

    if ($timeLog && isset($timeLog->time_in_out['time_in'])) {
        // 退勤時間を記録
        $timeLog->setTimeOut(now()->toDateTimeString());
        $timeLog->save();
        return redirect()->back()->with('success', '退勤を記録しました。');
    } else {
        return redirect()->back()->with('error', '出勤記録が見つかりませんでした。');
    }
}
    public function breakIn()
    {
        $user = Auth::user();
        $attendanceRecord = AttendanceRecord::where('user_id', $user->id)
                                            ->whereDate('date', now()->toDateString())
                                            ->first();

        if (!$attendanceRecord) {
            // 新しい AttendanceRecord を作成
            $attendanceRecord = new AttendanceRecord();
            $attendanceRecord->user_id = $user->id;
            $attendanceRecord->user_name = $user->name;
            $attendanceRecord->date = now()->toDateString();
            $attendanceRecord->save();
        }

        // BreakLog を作成
        $breakLog = new BreakLog();
        $breakLog->attendance_record_id = $attendanceRecord->id;
        $breakLog->break_in = now();
        $breakLog->save();

        return redirect()->back()->with('success', '休憩開始を記録しました。');
    }

    public function breakOut()
    {
        $user = Auth::user();
        $attendanceRecord = AttendanceRecord::where('user_id', $user->id)
                                            ->whereDate('date', now()->toDateString())
                                            ->first();

        if ($attendanceRecord) {
            $breakLog = BreakLog::where('attendance_record_id', $attendanceRecord->id)
                                ->latest()
                                ->first();

            if ($breakLog && $breakLog->break_in && !$breakLog->break_out) {
                $breakLog->break_out = now();
                $breakLog->save();
                return redirect()->back()->with('success', '休憩終了を記録しました。');
            } else {
                return redirect()->back()->with('error', '休憩開始記録が見つかりませんでした。');
            }
        } else {
            return redirect()->back()->with('error', '出勤記録が見つかりませんでした。');
        }
    }

    public function attendance()
{
    $loggedInUser = Auth::user();
    $currentDate = now()->toDateString(); // 現在の日付を取得
    $attendanceRecords = AttendanceRecord::where('user_id', $loggedInUser->id)->paginate(5);

    // 各 attendanceRecord に対して休憩時間と勤務時間を計算
    foreach ($attendanceRecords as $record) {
        // 休憩時間を計算
        $record->totalBreakTime = $this->calculateTotalBreakTime($record->breakLogs);

        // 勤務時間を計算
        $record->totalDutyTime = $this->calculateTotalDutyTime($record->timeLogs, [$record->totalBreakTime]);
    }

    return view('attendance', compact('attendanceRecords'));
}

    private function calculateTotalDutyTime($timeLogs, $totalBreakTime)
    {
        $totalDutyTimeSeconds = 0;

        foreach ($timeLogs as $timeLog) {
            $timeIn = isset($timeLog->time_in_out['time_in']) ? Carbon::parse($timeLog->time_in_out['time_in']) : null;
            $timeOut = isset($timeLog->time_in_out['time_out']) ? Carbon::parse($timeLog->time_in_out['time_out']) : null;

            if ($timeIn && $timeOut) {
                $dutyTimeSeconds = $timeOut->diffInSeconds($timeIn);

                $breakTimeSeconds = is_array($totalBreakTime) ? $this->convertToSeconds($totalBreakTime) : 0;

                $dutyTimeSeconds -= $breakTimeSeconds;
                $dutyTimeSeconds = max($dutyTimeSeconds, 0);

                $totalDutyTimeSeconds += $dutyTimeSeconds;
            }
        }

        \Log::info('Total Duty Time in Seconds: ' . $totalDutyTimeSeconds);

        return $this->convertToHMS($totalDutyTimeSeconds);
    }

    private function calculateTotalBreakTime($breakLogs)
    {
        $totalBreakTimeSeconds = 0;

        foreach ($breakLogs as $breakLog) {
            if ($breakLog->break_in && $breakLog->break_out) {
                $breakIn = Carbon::parse($breakLog->break_in);
                $breakOut = Carbon::parse($breakLog->break_out);
                $totalBreakTimeSeconds += $breakOut->diffInSeconds($breakIn);
            }
        }

        return $this->convertToHMS($totalBreakTimeSeconds);
    }

    private function BreakTime($attendanceRecords)
    {
        $totalBreakTimeSeconds = 0;

        foreach ($attendanceRecords as $attendanceRecord) {
            // $attendanceRecord が有効なオブジェクトであるかを確認
            if (is_object($attendanceRecord) && property_exists($attendanceRecord, 'id')) {
                $breakLogs = BreakLog::where('attendance_record_id', $attendanceRecord->id)->get();
                $totalBreakTimeSeconds += $this->convertToSeconds($this->calculateTotalBreakTime($breakLogs));
            }
        }
        \Log::info('Total Break Time: ' . $totalBreakTimeSeconds);

        return $this->convertToHMS($totalBreakTimeSeconds);
    }

    private function convertToSeconds($timeArray)
    {
        return (isset($timeArray['hours']) ? $timeArray['hours'] : 0) * 3600
             + (isset($timeArray['minutes']) ? $timeArray['minutes'] : 0) * 60
             + (isset($timeArray['seconds']) ? $timeArray['seconds'] : 0);
    }

    private function convertToHMS($totalSeconds)
    {
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return [
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
        ];
    }

    public function getAttendanceData($date)
    {
        $attendanceRecords = AttendanceRecord::whereDate('created_at', $date)->paginate(5);

        $totalBreakTime = $this->calculateTotalBreakTime($attendanceRecords->flatMap->breakLogs);
        $totalDutyTime = $this->calculateTotalDutyTime($attendanceRecords->flatMap->timeLogs, $totalBreakTime);

        return response()->json([
            'attendanceRecords' => $attendanceRecords,
            'totalBreakTime' => $totalBreakTime,
            'totalDutyTime' => $totalDutyTime,
        ]);
    }

    public function getDataByDate($date)
    {
        $parsedDate = Carbon::parse($date);
        $attendanceRecords = AttendanceRecord::with('user', 'timeLogs')
                                             ->whereDate('date', $parsedDate)
                                             ->get();

        $data = $attendanceRecords->map(function($record) {
            $totalBreakTime = $this->calculateTotalBreakTime($record->breakLogs);
            $totalDutyTime = $this->calculateTotalDutyTime($record->timeLogs, $totalBreakTime);
            return [
                'user' => $record->user ? $record->user->name : '-',
                'time_in' => $record->timeLogs->first() && isset($record->timeLogs->first()->time_in_out['time_in'])
                    ? Carbon::parse($record->timeLogs->first()->time_in_out['time_in'])->format('H:i:s') : '-',
                'time_out' => $record->timeLogs->first() && isset($record->timeLogs->first()->time_in_out['time_out'])
                    ? Carbon::parse($record->timeLogs->first()->time_in_out['time_out'])->format('H:i:s') : '-',
                'total_break_time' => $totalBreakTime,
                'total_duty_time' => $totalDutyTime,
            ];
        });

        return response()->json(['attendanceRecords' => $data]);
    }


}
