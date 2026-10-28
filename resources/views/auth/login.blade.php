@extends('layout.app')

@section('title', 'Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h1 class="h3 mb-4">Login</h1>
 
            <form method="POST" action="{{ route('login') }}">
                @csrf
 
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text"
                           class="form-control @error('username') is-invalid @enderror"
                           id="username" name="username"
                           value="{{ old('username') }}" required autofocus>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
 
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember"
                           @checked(old('remember'))>
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center text-muted small mt-3">
                Don't have an account? <a href="{{ route('register') }}">Register</a>
            </p>
        </div>
    </div>
@endsection