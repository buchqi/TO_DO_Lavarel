{{-- GroupController@create renders this view for GET /groups/create.
    The form submits new group data to GroupController@store. --}}
@extends('layouts.app')

{{-- @section sends this page body into the shared layout. --}}
@section('content')
    <div class="portal-header p-4 mb-4">
        <h1 class="h3 mb-1">Create Group</h1>
        <p class="mb-0">Start a team for shared coursework, clubs, or KIU events.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    {{-- POST is used because creating a group changes the database.
                        @csrf must be present or Laravel will reject the request. --}}
                    <form action="{{ route('groups.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            {{-- old('name') restores submitted input if validation
                                fails and Laravel redirects back to the form. --}}
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Group</button>
                            <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
