<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
</head>
@extends('layouts.auth')

@section('heading','สร้างบัญชีผู้ใช้ใหม่')
@section('subheading','สร้างบัญชีผู้ใช้ใหม่')

@section('content')
    @if($errors->any())
        <div class="error">{{ implode(', ', $errors->all()) }}</div>
    @endif

    <form method="POST" action="{{ route('register.post') }}">
        @csrf

        <div style="margin-bottom:10px">
            <label for="name">ชื่อ</label>
            <input id="name" name="name" required class="input">
        </div>

        <div style="margin-bottom:10px">
            <label for="email">อีเมล</label>
            <input id="email" name="email" type="email" required class="input">
        </div>

        <div class="row" style="margin-bottom:10px">
            <div style="flex:1">
                <label for="password">รหัสผ่าน</label>
                <input id="password" name="password" type="password" required class="input">
            </div>
            <div style="flex:1">
                <label for="password_confirmation">ยืนยัน</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="input">
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center">
            <a href="{{ route('login') }}" class="link">มีบัญชีอยู่แล้วใช่ไหม?</a>
            <button type="submit" class="btn">สมัครสมาชิก</button>
        </div>
    </form>
@endsection
