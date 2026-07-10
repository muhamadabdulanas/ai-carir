@extends('layouts.app')

@section('title', 'Masuk ke AI-Carir')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="auth-header">
            <h1 class="auth-title"><span class="gradient-text">Selamat Datang</span></h1>
            <p class="auth-subtitle">Silakan masuk ke akun AI-Carir Anda</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                @error('email')
                    <span style="color: var(--danger); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <div class="flex justify-between" style="margin-bottom: 8px;">
                    <label for="password" class="form-label" style="margin-bottom: 0;">Password</label>
                </div>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
                <i data-feather="log-in" style="width: 18px; height: 18px;"></i>
                Masuk Sekarang
            </button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar Disini</a>
        </div>
    </div>
</div>
@endsection
