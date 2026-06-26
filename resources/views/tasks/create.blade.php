{{-- TaskController@create renders this view for GET /tasks/create.
    It passes $tags and $groups so the form can build relationship inputs. --}}
@extends('layouts.app')

{{-- This content section is inserted into layouts.app at @yield('content'). --}}
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
                    {{-- The form posts to TaskController@store.
                        enctype="multipart/form-data" is required for file uploads;
                        without it, the attachment would not reach Laravel. --}}
                    <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- The form partial is reused by create and edit so both
                            screens share the same fields and validation display. --}}
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
