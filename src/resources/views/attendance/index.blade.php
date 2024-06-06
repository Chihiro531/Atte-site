@extends('layouts/app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <style>
        :disabled {
            background-color: rgba(221, 221, 221, 0.679);
        }
    </style>
@endsection

@section('content')

    <header id=header class="header_header">
        <h1>Atte</h1>
        <nav class="header_nav">
            <ul>
                <li><a href="/">ホーム</a></li>
                <li><a href="/userlist">ユーザー一覧</a></li>
                <li><a href="/attendance">日付一覧</a></li>
                <form method="post" action="/logout">
                    @csrf
                <li><button type="submit">ログアウト</button></li>
                </form>
            </ul>
        </nav>
    </header>
    <main id="main">
        <div class="main_comment">
        <p><?php $user = Auth::user(); ?>{{ $user->name }}さんお疲れ様です！</p>
        </div>
        <div class="attendance__alert">
        @if (session('success'))
            <div class="attendance_alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="attendance_alert--error">{{ session('error') }}</div>
        @endif
        <div class="push_button">
            <ul class="upper">
                <li>
                    <form id="timeInForm" method="post" action="/TimeIn">
                        @csrf
                    <button type="submit" class="timein_btn custom_btn"<?php if($is_syukkin){echo ' ';};?>>勤務開始</button>
                    </form>
                </li>
                <li>
                    <form id="timeOutForm" method="post" action="/TimeOut">
                        @csrf
                    <button type="submit" name="time_out" class="timeout_btn custom_btn"<?php if(!$is_syukkin){echo ' disabled';};?>>勤務終了</button>
                    </form>
                </li>
            </ul>
            <ul class="below">
                <li>
                    <form method="post" action="/BreakIn">
                        @csrf
                    <button type="submit" name="break_in" class="breakin_btn"<?php if(!$is_syukkin){echo ' disabled';};?>>休憩開始</button>
                    </form>
                </li>
                <li>
                    <form method="post" action="/BreakOut">
                        @csrf
                    <button type="submit" class="breakout_btn"<?php if(!$is_syukkin){echo ' disabled';};?>>休憩終了</button>
                    </form>
                </li>
            </ul>
        </div>
    </main>
@endsection



