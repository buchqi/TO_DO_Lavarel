@extends('layouts.app')

@section('content')
    <div class="portal-header p-4 mb-4">
        <h1 class="h3 mb-1">Create Task</h1>
        <p class="mb-0">Add a class deadline, club activity, KIU event, or personal study item.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h1 class="h5 mb-0">Create New Task</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @include('tasks._form')

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Task</button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
