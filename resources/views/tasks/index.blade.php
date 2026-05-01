@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 class="h3 mb-0">Tasks</h1>
        <a href="{{ route('tasks.create') }}" class="btn btn-success">+ New Task</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div id="ajax-status-message"></div>

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('tasks.index') }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
        <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">Pending</a>
        <a href="{{ route('tasks.index', ['status' => 'done']) }}" class="btn btn-sm {{ $status === 'done' ? 'btn-success' : 'btn-outline-success' }}">Done</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Description</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>
                                <span class="badge js-status-badge {{ $task->status === 'done' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($task->status) }}
                                </span>
                            </td>
                            <td>{{ $task->deadline->format('Y-m-d') }}</td>
                            <td>{{ $task->description ?: '-' }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <form action="{{ route('tasks.update', $task) }}" method="POST" class="js-status-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="quick_status" value="1">
                                        <input type="hidden" name="status" class="js-status-input" value="{{ $task->status === 'pending' ? 'done' : 'pending' }}">
                                        <input type="hidden" name="status_filter" value="{{ $status }}">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary js-status-btn">
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
                            <td colspan="5" class="text-center py-4">No tasks yet. Create your first task.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Intercept status forms and update UI in-place after a successful response.
        document.querySelectorAll('.js-status-form').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = form.querySelector('.js-status-btn');
                const statusInput = form.querySelector('.js-status-input');
                const row = form.closest('tr');
                const badge = row.querySelector('.js-status-badge');
                const messageBox = document.getElementById('ajax-status-message');
                const csrfToken = form.querySelector('input[name="_token"]').value;
                const targetStatus = statusInput.value;

                button.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                        },
                        body: new URLSearchParams({
                            _method: 'PATCH',
                            quick_status: '1',
                            status: targetStatus,
                            status_filter: form.querySelector('input[name="status_filter"]').value
                        }).toString()
                    });

                    if (!response.ok) {
                        throw new Error('Request failed.');
                    }

                    const data = await response.json();
                    const newStatus = data.status;

                    // Keep badge, hidden input, and button label in sync with server state.
                    badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    badge.classList.remove('bg-success', 'bg-warning', 'text-dark');
                    if (newStatus === 'done') {
                        badge.classList.add('bg-success');
                        statusInput.value = 'pending';
                        button.textContent = 'Mark Pending';
                    } else {
                        badge.classList.add('bg-warning', 'text-dark');
                        statusInput.value = 'done';
                        button.textContent = 'Mark Done';
                    }

                    messageBox.innerHTML = '<div class="alert alert-success">Task status updated successfully.</div>';
                } catch (error) {
                    messageBox.innerHTML = '<div class="alert alert-danger">Could not update task status. Please try again.</div>';
                } finally {
                    button.disabled = false;
                }
            });
        });
    </script>
@endsection
