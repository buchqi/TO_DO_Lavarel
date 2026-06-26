{{-- LoginController@create renders this view for GET /login.
    The form posts credentials to LoginController@store. --}}
@extends('layouts.app')

{{-- @section fills the @yield('content') placeholder from layouts.app. --}}
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="portal-header p-4 mb-4">
                <h1 class="h3 mb-2">KIU Student Portal</h1>
                <p class="mb-0">Sign in to manage your academic tasks, activities, and deadlines.</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Login</h2>
                    {{-- Login uses POST because it changes session state.
                        @csrf protects the request from cross-site form submission. --}}
                    <form action="{{ route('login.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            {{-- old('email') repopulates the field after validation
                                failure, while @error displays the controller's error. --}}
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
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

                        <div class="form-check mb-4">
                            {{-- This checkbox becomes the remember argument passed
                                to Auth::attempt() in LoginController. --}}
                            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                            <a href="{{ route('register') }}" class="btn btn-outline-secondary">Create student account</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
