<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// TaskController is the main resource controller for tasks.
// Resource controllers are Laravel's MVC convention for grouping CRUD actions
// in one class: index, create, store, edit, update, and destroy.
class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Called by GET /tasks from the resource route.
        // It receives optional query-string filters, uses Task, Group, and Tag
        // relationships indirectly, and returns the tasks.index Blade view.
        $status = request('status', 'all');
        // The user should only see personal tasks and tasks from groups they
        // own or belong to, so we calculate accessible group ids first.
        $groupIds = $this->accessibleGroupIds();
        // The base query is cloned later for counts. Reusing one access rule
        // keeps the list and counters consistent.
        $baseQuery = $this->accessibleTaskQuery($groupIds);

        // Apply status filter only when a valid filter is selected.
        $tasks = $this->accessibleTaskQuery($groupIds)
            // Eager loading prevents the N+1 query problem when Blade displays
            // tags and group names for every task.
            ->with(['tags', 'group'])
            ->when(in_array($status, ['pending', 'done'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('deadline')
            ->latest()
            ->get();

        $taskCounts = [
            'all' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'done' => (clone $baseQuery)->where('status', 'done')->count(),
        ];

        return view('tasks.index', compact('tasks', 'status', 'taskCounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Called by GET /tasks/create.
        // It prepares the data needed by the form and returns tasks.create.
        // No Task is saved here because GET requests should not change data.
        $tags = Tag::orderBy('name')->get();
        $groups = $this->availableGroups();

        return view('tasks.create', compact('tags', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Called by POST /tasks.
        // It receives form data from Blade, validates it, uses the Task model
        // plus the tag pivot table, and returns a redirect after saving.
        $validated = $this->validatedTask($request);
        $tagIds = $validated['tags'] ?? [];
        // tags and attachment are handled separately because they are not plain
        // columns on the tasks table: tags are pivot rows and attachment is a file.
        unset($validated['tags'], $validated['attachment']);

        // Laravel file uploads are represented by UploadedFile objects.
        // store('tasks', 'public') writes the file to storage/app/public/tasks
        // and returns the relative path that can be saved in the database.
        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('tasks', 'public');
        }

        // Eloquent creates the database row. auth()->id() ties the task to
        // the currently logged-in user, which is needed for ownership checks.
        $task = Task::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        // sync() writes the many-to-many task_tag pivot records.
        // Without this line, the task would save but selected tags would not.
        $task->tags()->sync($tagIds);

        // Redirect-after-POST is a common web pattern that prevents duplicate
        // submissions when the user refreshes the browser.
        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // Called by GET /tasks/{task}/edit.
        // Laravel route model binding has already loaded the Task model from
        // the URL id before this method runs.
        $this->authorizeTaskManager($task);

        // Load related data for the edit form so Blade can show selected tags
        // and the group name without extra queries per relationship.
        $task->load(['tags', 'group']);
        $tags = Tag::orderBy('name')->get();
        $groups = $this->availableGroups();

        return view('tasks.edit', compact('task', 'tags', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // Called by PUT/PATCH /tasks/{task}.
        // It receives form data for an existing Task, updates database columns
        // and pivot rows, and returns either JSON or a browser redirect.
        $this->authorizeTaskManager($task);

        // This branch handles quick status toggles from the list page.
        if ($request->boolean('quick_status')) {
            // A smaller validation set is used here because the request changes
            // only status. Requiring the full edit form would make quick updates fail.
            $validated = $request->validate([
                'status' => ['required', 'in:pending,done'],
            ], [
                'status.required' => 'Please choose a task status.',
                'status.in' => 'Status must be either pending or done.',
            ]);

            $task->update($validated);

            // Return JSON for AJAX calls so the page can update without reload.
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Task status updated successfully.',
                    'status' => $task->status,
                ]);
            }

            return redirect()
                ->route('tasks.index', ['status' => $request->input('status_filter', 'all')])
                ->with('success', 'Task status updated successfully.');
        }

        // Full update path: validate all fields, handle a possible replacement
        // upload, update the task row, then sync the many-to-many tags.
        $validated = $this->validatedTask($request);
        $tagIds = $validated['tags'] ?? [];
        unset($validated['tags'], $validated['attachment']);

        // If a new file is uploaded, delete the old file first so storage does
        // not collect unused attachments.
        if ($request->hasFile('attachment')) {
            $this->deleteAttachment($task);
            $validated['attachment_path'] = $request->file('attachment')->store('tasks', 'public');
        }

        $task->update($validated);
        $task->tags()->sync($tagIds);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function toggle(Task $task)
    {
        // Called by PATCH /tasks/{task}/toggle from a small Blade form.
        // It receives only the task id through route model binding, flips the
        // status, and redirects back to the task list.
        $this->authorizeTaskToggler($task);

        $task->update([
            'status' => $task->status === 'pending' ? 'done' : 'pending',
        ]);

        return redirect()
            ->route('tasks.index', ['status' => request('status_filter', 'all')])
            ->with('success', 'Task status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Called by DELETE /tasks/{task}.
        // It receives an existing Task through route model binding, checks
        // permission, removes any stored file, deletes the row, and redirects.
        $this->authorizeTaskManager($task);
        $this->deleteAttachment($task);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    private function validatedTask(Request $request): array
    {
        // This shared validator keeps create and update rules identical.
        // Laravel automatically redirects back with errors if validation fails,
        // which stops invalid data before it reaches Eloquent or the database.
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,done'],
            'deadline' => ['required', 'date'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ], [
            'title.required' => 'Please enter a task title.',
            'status.required' => 'Please choose a task status.',
            'status.in' => 'Status must be either pending or done.',
            'deadline.required' => 'Please choose a deadline date.',
            'deadline.date' => 'Deadline must be a valid date.',
            'group_id.exists' => 'Please choose a valid group.',
            'attachment.mimes' => 'Attachment must be a PDF, image, or Word document.',
            'attachment.max' => 'Attachment must not be larger than 5MB.',
        ]);

        // Validation can prove that a group id exists, but authorization must
        // prove that the current user may use that group.
        if (! empty($validated['group_id']) && ! in_array((int) $validated['group_id'], $this->accessibleGroupIds(), true)) {
            abort(403);
        }

        // Empty string values from HTML selects should become null so the
        // database represents "personal task" consistently.
        $validated['group_id'] = $validated['group_id'] ?: null;

        return $validated;
    }

    private function authorizeTaskManager(Task $task): void
    {
        // This method is a controller-level authorization gate.
        // It exists so edit, update, and delete all enforce the same rule.
        // Managing a task means changing its details or removing it entirely,
        // so invited group members are intentionally excluded from this permission.
        $task->loadMissing('group');

        // Personal tasks may be managed only by their creator.
        $canManagePersonalTask = $task->group_id === null && $task->user_id === auth()->id();
        // Shared tasks require group membership first.
        $canAccessSharedGroup = $task->group_id !== null && $task->group?->hasMember(auth()->user());
        // Even inside a shared group, only the task creator or group owner can
        // manage the task. Other members can view but not edit.
        $canManageSharedTask = $task->group_id !== null
            && $canAccessSharedGroup
            && ($task->user_id === auth()->id() || $task->group?->owner_id === auth()->id());

        // abort_unless throws a 403 HTTP response. If this line were removed,
        // users could edit or delete tasks they do not own.
        abort_unless($canManagePersonalTask || $canManageSharedTask, 403);
    }

    private function authorizeTaskToggler(Task $task): void
    {
        // Toggling is intentionally less powerful than managing.
        // A group member who participates in shared work may mark a task pending/done,
        // but that does not mean they can edit the title, deadline, tags, or delete it.
        $task->loadMissing('group');

        // Personal tasks are private, so only the creator can change their status.
        $canTogglePersonalTask = $task->group_id === null && $task->user_id === auth()->id();

        // Shared tasks are collaborative: the creator, group owner, and invited
        // members may all participate by updating status through the toggle route.
        $canToggleSharedTask = $task->group_id !== null
            && $task->group !== null
            && ($task->user_id === auth()->id() || $task->group->hasMember(auth()->user()));

        // If this guard were removed, unrelated users could guess task ids and
        // mark other people's tasks pending/done.
        abort_unless($canTogglePersonalTask || $canToggleSharedTask, 403);
    }

    private function deleteAttachment(Task $task): void
    {
        // Laravel's Storage facade abstracts the filesystem. The controller
        // does not need to know the real disk path, only the configured disk.
        if ($task->attachment_path && Storage::disk('public')->exists($task->attachment_path)) {
            Storage::disk('public')->delete($task->attachment_path);
        }
    }

    private function availableGroups()
    {
        // This query returns groups the current user may assign tasks to:
        // groups they own plus groups where they appear in the group_user pivot.
        return Group::query()
            ->where('owner_id', auth()->id())
            ->orWhereHas('users', fn ($query) => $query->where('users.id', auth()->id()))
            ->orderBy('name')
            ->get();
    }

    private function accessibleGroupIds(): array
    {
        // Controllers often convert Eloquent collections into simple arrays
        // when the next query needs ids for whereIn().
        return $this->availableGroups()->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    private function accessibleTaskQuery(array $groupIds)
    {
        // This reusable query is the core data-access rule for the task list.
        // It combines personal ownership with shared group membership.
        return Task::query()
            ->where(function ($query) use ($groupIds) {
                // Personal tasks must belong to the logged-in user and have no group.
                $query->where(function ($personalQuery) {
                    $personalQuery->where('user_id', auth()->id())
                        ->whereNull('group_id');
                });

                // Shared tasks are included only when their group is accessible.
                if ($groupIds !== []) {
                    $query->orWhereIn('group_id', $groupIds);
                }
            });
    }
}
