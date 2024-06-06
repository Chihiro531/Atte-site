@extends('layouts/app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

    <header id=header class="header_header">
        <h1>Atte</h1>
    </header>

    <main id="main" class="wrapper">
        <h2 class="main_form_title">ログイン</h2>

        <div class="form__error">
            @if (session('loginError'))
        <div style="color: red;">
            {{ session('loginError') }}
        </div>
    @endif
        </div>

        <form class="login_form" action="/login" method="post">
                @csrf
            <ul>
            <li class="login_form_email">
            <input type="email" name="email" placeholder="メールアドレス" value="{{ old('email') }}" />
            </li>
            <li class="login_form_password">
            <input type="password" name="password" placeholder="パスワード" >
            </li>
            <li class="login_form_sbt">
            <button type="submit">ログイン</button>
            </li>
            </ul>
        </form>
        <p>アカウントをお持ちでない方はこちら</p>
        <a href="/register">会員登録</a>
    </main>
@endsection
