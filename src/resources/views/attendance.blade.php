@php
use Carbon\Carbon;
@endphp

@extends('layouts/app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')

    <header id=header class="header_header">
        <h1>Atte</h1>
        <nav class="header_nav">
            <ul>
                <li><a href="/">ホーム</a></li>
                <li><a href="/attendance">日付一覧</a></li>
                <form method="post" action="/logout">
                    @csrf
                <li><button type="submit">ログアウト</button></li>
                </form>
            </ul>
        </nav>
    </header>
    <body>
    <main id="main">
        <div class="data_change">
    <button id="prevDate">&lt;</button>
    <p id="currentDate">{{ Carbon::now()->format('Y-m-d') }}</p>
    <button id="nextDate">&gt;</button>
</div>

                <table class="attendance__table">
                    <thead>
                    <tr class="attendance__row">
                        <th class="attendance__label">名前</th>
                        <th class="attendance__label">勤務開始</th>
                        <th class="attendance__label">勤務終了</th>
                        <th class="attendance__label">休憩時間</th>
                        <th class="attendance__label">勤務時間</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($attendanceRecords->take(5) as $record)
                    <tr class="attendance__row">
                    <td class="attendance__row">{{ $record->user->name }}</td>
                    <td class="attendance__row">
                        {{ $record->timeLogs->first() ? $record->timeLogs->first()->getTimeIn() : '-' }}
                    </td>
                    <td class="attendance__row">
                        {{ $record->timeLogs->first() ? $record->timeLogs->first()->getTimeOut() : '-' }}
                    </td>
                    <td class="attendance__row">
                        {{ $record->totalBreakTime['hours'] ?? '0' }}:{{ $record->totalBreakTime['minutes'] ?? '0' }}:{{ $record->totalBreakTime['seconds'] ?? '0' }}
                    </td>
                    <td class="attendance__row">
                        {{ $record->totalDutyTime['hours'] ?? '0' }}:{{ $record->totalDutyTime['minutes'] ?? '0' }}:{{ $record->totalDutyTime['seconds'] ?? '0' }}
                    </td>
                </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination">
                    @if ($attendanceRecords->currentPage() > 1)
                        <a href="{{ $attendanceRecords->previousPageUrl() }}" rel="prev">&laquo;</a>
                    @endif

                    @for ($i = 1; $i <= $attendanceRecords->lastPage(); $i++)
                        <a href="{{ $attendanceRecords->url($i) }}" class="{{ ($attendanceRecords->currentPage() == $i) ? 'active' : '' }}">{{ $i }}</a>
                    @endfor

                    @if ($attendanceRecords->hasMorePages())
                        <a href="{{ $attendanceRecords->nextPageUrl() }}" rel="next">&raquo;</a>
                    @endif
                </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentDateElement = document.getElementById('currentDate');
        const prevDateButton = document.getElementById('prevDate');
        const nextDateButton = document.getElementById('nextDate');

        let currentDate = new Date(currentDateElement.innerText);

        function updateDateDisplay(date) {
            currentDateElement.innerText = date.toISOString().split('T')[0];
            changeDate(currentDateElement.innerText);
        }

        prevDateButton.addEventListener('click', function() {
            currentDate.setDate(currentDate.getDate() - 1);
            updateDateDisplay(currentDate);
        });

        nextDateButton.addEventListener('click', function() {
            currentDate.setDate(currentDate.getDate() + 1);
            updateDateDisplay(currentDate);
        });

        function changeDate(date) {
            $.ajax({
                url: `/attendance/data/${date}`,
                type: 'GET',
                success: function(response) {
                    displayAttendanceData(response.attendanceRecords);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function displayAttendanceData(data) {
            const tableBody = document.querySelector('.attendance__table tbody');
            tableBody.innerHTML = ''; // テーブルの内容をクリア

            data.forEach(record => {
                const row = document.createElement('tr');
                row.classList.add('attendance__row');

                const userNameCell = document.createElement('td');
                userNameCell.classList.add('attendance__row');
                userNameCell.innerText = record.user;

                const timeInCell = document.createElement('td');
                timeInCell.classList.add('attendance__row');
                timeInCell.innerText = record.time_in;

                const timeOutCell = document.createElement('td');
                timeOutCell.classList.add('attendance__row');
                timeOutCell.innerText = record.time_out;

                const totalBreakTimeCell = document.createElement('td');
                totalBreakTimeCell.classList.add('attendance__row');
                totalBreakTimeCell.innerText = `${record.total_break_time.hours}:${record.total_break_time.minutes}:${record.total_break_time.seconds}`;

                const totalDutyTimeCell = document.createElement('td');
                totalDutyTimeCell.classList.add('attendance__row');
                totalDutyTimeCell.innerText = `${record.total_duty_time.hours}:${record.total_duty_time.minutes}:${record.total_duty_time.seconds}`;

                row.appendChild(userNameCell);
                row.appendChild(timeInCell);
                row.appendChild(timeOutCell);
                row.appendChild(totalBreakTimeCell);
                row.appendChild(totalDutyTimeCell);

                tableBody.appendChild(row);
            });
        }
    });
</script>
    </body>
@endsection
