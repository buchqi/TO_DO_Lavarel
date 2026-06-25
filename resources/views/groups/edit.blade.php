@extends('layouts.app')

@section('content')
    <div class="portal-header p-4 mb-4">
        <h1 class="h3 mb-1">Edit Group</h1>
        <p class="mb-0">Update the group name or description.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('groups.update', $group) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $group->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $group->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update Group</button>
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>

                    <form action="{{ route('groups.destroy', $group) }}" method="POST" class="mt-3" onsubmit="return confirm('Delete this group? Shared tasks will become personal to their creators.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Delete Group</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
