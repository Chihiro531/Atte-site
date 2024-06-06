@extends('layouts/app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')

    <header id=header class="header_header">
        <h1>Atte</h1>
    </header>

    <main id="main" class="wrapper">
        <h2 class="main_form_title">会員登録</h2>
        <form class="register_form" action="/register" method="post">
                @csrf
            <ul>
            <li class="register_form_name">
            <input type="text" name="name" placeholder="名前" value="{{ old('name') }}"/>
            </li>
            <div class="form__error">
                @error('name')
                {{ $message }}
                @enderror
            </div>
            <li class="register_form_email">
            <input type="email" name="email" placeholder="メールアドレス" value="{{ old('email') }}"/>
            </li>
            <div class="form__error">
                @error('email')
                {{ $message }}
                @enderror
            </div>
            <li class="register_form_password">
            <input type="password" name="password" placeholder="パスワード"/>
            </li>
            <div class="form__error">
                @error('password')
                {{ $message }}
                @enderror
            </div>
            <li class="register_form_confirm">
            <input type="password" name="password_confirmation" placeholder="確認用パスワード"/>
            </li>
            <div class="form__error">
                @error('password_confirmation')
                {{ $message }}
                @enderror
            </div>
            <li class="register_form_sbt">
            <button type="submit">会員登録</button>
            </li>
            </ul>
        </form>
        <p>アカウントをお持ちの方はこちらから</p>
        <a href="/login">ログイン</a>
    </main>
@endsection
