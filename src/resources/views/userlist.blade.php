@extends('layouts/app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/userlist.css') }}">
    <style>
        .table-wrapper {
            max-width: 860px; /* テーブルの最大幅を指定 */
            display: flex;
            flex-wrap: wrap; /* テーブルを横方向に折り返す */
            padding-left:7%;
            padding-top:5%;
        }
        .table-wrapper table {
            border-collapse: collapse;
            width: 100%; /* テーブルの幅を100%に設定 */
            margin-bottom: 20px; /* テーブル間の余白を設定 */
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            height: 40px; /* 行の高さを変更 */
            white-space: nowrap;
        }
        th {
            background-color: #f2f2f2; /* ヘッダーの背景色 */
        }
        th span {
            display: block; /* 日付を行ごとに表示 */
        }
        th[title] {
            position: relative;
        }
        th[title]:hover::after {
            content: attr(title);
            position: absolute;
            top: -1.5em;
            left: 50%;
            transform: translateX(-50%);
            padding: 2px 8px;
            background-color: black;
            color: white;
            white-space: nowrap;
            border-radius: 3px;
            z-index: 10;
        }
    </style>
@endsection

@section('content')

<header id="header" class="header_header">
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

<main id="main">
    <div class="table-wrapper">
        <!-- 1日から8日までのテーブル -->
        <table>
            <thead>
                <tr>
                    <th><span>名前</span></th>
                    @foreach($dates as $index => $date)
                        @if ($index < 8)
                            <th><span>{{ $date['display'] }}</span></th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceData as $data)
                    <tr>
                        <td>{{ $data['user']->name }}</td>
                        @foreach($data['attendance'] as $index => $status)
                            @if ($index < 8)
                                <td>{{ $status }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- 9日から15日までのテーブル -->
        <table>
            <thead>
                <tr>
                    <th><span>名前</span></th>
                    @foreach($dates as $index => $date)
                        @if ($index >= 8 && $index < 15)
                            <th><span>{{ $date['display'] }}</span></th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceData as $data)
                    <tr>
                        <td>{{ $data['user']->name }}</td>
                        @foreach($data['attendance'] as $index => $status)
                            @if ($index >= 8 && $index < 15)
                                <td>{{ $status }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- 16日から23日までのテーブル -->
        <table>
            <thead>
                <tr>
                    <th><span>名前</span></th>
                    @foreach($dates as $index => $date)
                        @if ($index >= 15 && $index < 23)
                            <th><span>{{ $date['display'] }}</span></th>
                        @endif
                        @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceData as $data)
                    <tr>
                        <td>{{ $data['user']->name }}</td>
                        @foreach($data['attendance'] as $index => $status)
                            @if ($index >= 15 && $index < 23)
                                <td>{{ $status }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- 24日から30日までのテーブル -->
        <table>
            <thead>
                <tr>
                    <th><span>名前</span></th>
                    @foreach($dates as $index => $date)
                        @if ($index >= 23)
                            <th><span>{{ $date['display'] }}</span></th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceData as $data)
                    <tr>
                        <td>{{ $data['user']->name }}</td>
                        @foreach($data['attendance'] as $index => $status)
                            @if ($index >= 23)
                                <td>{{ $status }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>
@endsection

