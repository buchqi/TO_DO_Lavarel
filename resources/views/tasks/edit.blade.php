{{-- TaskController@edit renders this view for GET /tasks/{task}/edit.
    It passes $task, $tags, and $groups so existing values can be edited. --}}
@extends('layouts.app')

{{-- @section defines the page body that layouts.app will render. --}}
@section('content')
    <div class="portal-header p-4 mb-4">
        <h1 class="h3 mb-1">Edit Task</h1>
        <p class="mb-0">Update your task details, status, tags, or supporting file.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h1 class="h5 mb-0">Edit Task</h1>
                </div>
                <div class="card-body">
                    {{-- The form submits to TaskController@update.
                        HTML forms cannot send PUT directly, so @method('PUT')
                        tells Laravel to treat the POST as a PUT request. --}}
                    <form action="{{ route('tasks.update', $task) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- The same form partial is used here as create.blade.php,
                            proving how Blade partials reduce duplicated markup. --}}
                        @include('tasks._form')

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Task</button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
