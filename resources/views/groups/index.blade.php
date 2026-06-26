{{-- GroupController@index renders this view for GET /groups.
    It passes $ownedGroups and $memberGroups from authenticated-user relationships. --}}
@extends('layouts.app')

{{-- This section fills the main content area in layouts.app. --}}
@section('content')
    <div class="portal-header p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-2">Groups</h1>
                <p class="mb-0">Create teams for shared KIU tasks and activities.</p>
            </div>
            <a href="{{ route('groups.create') }}" class="btn btn-warning">Create Group</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h2 class="h5 mb-0">Groups You Own</h2>
                </div>
                <div class="card-body">
                    {{-- @forelse loops through owned groups and also provides an
                        empty message for users who have not created any groups. --}}
                    @forelse ($ownedGroups as $group)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <h3 class="h6 mb-1">
                                        <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
                                    </h3>
                                    <p class="text-muted mb-1">{{ $group->description ?: 'No description.' }}</p>
                                    <span class="badge bg-primary">{{ $group->tasks_count }} tasks</span>
                                </div>
                                <a href="{{ route('groups.edit', $group) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">You have not created any groups yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h2 class="h5 mb-0">Groups You Joined</h2>
                </div>
                <div class="card-body">
                    {{-- These groups come from the User::groups() belongsToMany
                        relationship through the group_user pivot table. --}}
                    @forelse ($memberGroups as $group)
                        <div class="border rounded p-3 mb-3">
                            <h3 class="h6 mb-1">
                                <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
                            </h3>
                            <p class="text-muted mb-1">Owner: {{ $group->owner->name }}</p>
                            <span class="badge bg-primary">{{ $group->tasks_count }} tasks</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">You are not a member of any groups yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
