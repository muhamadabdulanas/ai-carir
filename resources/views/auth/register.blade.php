@extends('layouts.app')

@section('title', 'Daftar Akun AI-Carir')

@section('content')
<div class="auth-wrapper">
    <div class="card auth-card">
        <div class="auth-header">
            <h1 class="auth-title"><span class="gradient-text">Daftar Mahasiswa</span></h1>
            <p class="auth-subtitle">Buat akun untuk memulai analisis karir berbasis AI</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Budi Santoso" required autofocus>
                @error('name')
                    <span style="color: var(--danger); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="budi@email.com" required>
                @error('email')
                    <span style="color: var(--danger); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                @error('password')
                    <span style="color: var(--danger); font-size: 0.85rem; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password Anda" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i data-feather="user-plus" style="width: 18px; height: 18px;"></i>
                Daftar Akun
            </button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk Disini</a>
        </div>
    </div>
</div>
@endsection
