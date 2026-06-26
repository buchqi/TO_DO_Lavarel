{{-- GroupController@show renders this view for GET /groups/{group}.
    It passes one $group with owner, users, and tasks eager-loaded. --}}
@extends('layouts.app')

{{-- The group detail page is inserted into the shared application layout. --}}
@section('content')
    @php
        // This value controls owner-only buttons in the UI.
        // Controllers still enforce authorization because Blade checks are only presentation.
        $isOwner = $group->owner_id === auth()->id();
    @endphp

    <div class="portal-header p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-2">{{ $group->name }}</h1>
                <p class="mb-0">{{ $group->description ?: 'No description provided.' }}</p>
            </div>
            {{-- Only owners should see group editing controls. --}}
            @if ($isOwner)
                <a href="{{ route('groups.edit', $group) }}" class="btn btn-warning">Edit Group</a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header">
                    <h2 class="h5 mb-0">People</h2>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Owner:</strong> {{ $group->owner->name }}</p>

                    <h3 class="h6 mt-4">Members</h3>
                    {{-- $group->users is the belongsToMany relationship loaded
                        from the group_user pivot table. --}}
                    @forelse ($group->users as $member)
                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2 gap-2">
                            <div>
                                <div>{{ $member->name }}</div>
                                <div class="text-muted small">{{ $member->email }}</div>
                            </div>
                            @if ($isOwner)
                                {{-- Removing a member sends DELETE to a custom route
                                    that calls GroupController@removeMember. --}}
                                <form action="{{ route('groups.members.destroy', [$group, $member]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No members yet.</p>
                    @endforelse
                </div>
            </div>

            @if ($isOwner)
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h2 class="h5 mb-0">Add Member</h2>
                    </div>
                    <div class="card-body">
                        {{-- Adding a member posts an email address.
                            The controller finds the User and attaches it through the pivot table. --}}
                        <form action="{{ route('groups.members.store', $group) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">User Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Add Member</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center gap-3">
                    <h2 class="h5 mb-0">Shared Tasks</h2>
                    <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-outline-primary">Create Task</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Creator</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Shared tasks come from the Group::tasks() hasMany relationship. --}}
                            @forelse ($group->tasks as $task)
                                @php
                                    // Owners can manage all shared tasks; creators can manage
                                    // their own tasks. Other members remain view-only.
                                    $canManageTask = $task->user_id === auth()->id() || $isOwner;
                                @endphp
                                <tr>
                                    <td>{{ $task->title }}</td>
                                    <td>
                                        <span class="badge {{ $task->status === 'done' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $task->deadline->format('Y-m-d') }}</td>
                                    <td>{{ $task->user->name }}</td>
                                    <td class="text-end">
                                        @if ($canManageTask)
                                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        @else
                                            <span class="text-muted small">View only</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No shared tasks in this group yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
