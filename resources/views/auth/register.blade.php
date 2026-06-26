{{-- RegisterController@create renders this view for GET /register.
    The submitted form is handled by RegisterController@store. --}}
@extends('layouts.app')

{{-- This section becomes the main page content inside the shared layout. --}}
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="portal-header p-4 mb-4">
                <h1 class="h3 mb-2">Create KIU Student Account</h1>
                <p class="mb-0">Register to keep your own tasks private and organized.</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    {{-- Registration uses POST because it creates a users table row
                        and starts an authenticated session after success. --}}
                    <form action="{{ route('register.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            {{-- Laravel's confirmed validation rule expects this
                                exact password_confirmation field name. --}}
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Register</button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Already have an account?</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
