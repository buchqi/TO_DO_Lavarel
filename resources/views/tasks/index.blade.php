@extends('layouts.app')

@section('content')
    <div class="portal-header p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-2">KIU Student Task & Activity Management System</h1>
                <p class="mb-0">Track coursework, deadlines, student club activities, and KIU events in one place.</p>
            </div>
            <a href="{{ route('tasks.create') }}" class="btn btn-warning">Create Task</a>
        </div>
    </div>

    <div id="ajax-status-message"></div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">All Tasks</div>
                    <div class="h3 mb-0">{{ $taskCounts['all'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Pending</div>
                    <div class="h3 mb-0">{{ $taskCounts['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Done</div>
                    <div class="h3 mb-0">{{ $taskCounts['done'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('tasks.index') }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
        <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">Pending</a>
        <a href="{{ route('tasks.index', ['status' => 'done']) }}" class="btn btn-sm {{ $status === 'done' ? 'btn-success' : 'btn-outline-success' }}">Done</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Tags</th>
                        <th>Attachment</th>
                        <th>Description</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td class="fw-semibold">{{ $task->title }}</td>
                            <td>
                                <span class="badge js-status-badge {{ $task->status === 'done' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </td>
                            <td>{{ $task->deadline->format('Y-m-d') }}</td>
                            <td>
                                @forelse ($task->tags as $tag)
                                    <span class="badge tag-pill">{{ $tag->name }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($task->attachment_path)
                                    <a href="{{ asset('storage/'.$task->attachment_path) }}" target="_blank">Open</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $task->description ?: '-' }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status_filter" value="{{ $status }}">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            Mark {{ $task->status === 'pending' ? 'Done' : 'Pending' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form
                                        action="{{ route('tasks.destroy', $task) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this task?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No tasks yet. Create your first KIU task.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
