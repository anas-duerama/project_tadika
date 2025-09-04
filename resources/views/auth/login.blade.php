<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>
@extends('layouts.auth')

@section('heading','ระบบตาดีกาปือเราะ')
@section('subheading','เข้าสู้ระบบตาดีกาปือเราะ')

@section('content')
    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div style="margin-bottom:10px">
            <label for="email">อีเมล</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input">
        </div>

        <div style="margin-bottom:14px">
            <label for="password">รหัส</label>
            <input id="password" name="password" type="password" required class="input">
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center">
            <a href="{{ route('register') }}" class="link">สร้างบัญชีผู้ใช้ใหม่</a>
            <button type="submit" class="btn">เข้าสู่ระบบ</button>
        </div>
    </form>
@endsection
</body>
</html>
