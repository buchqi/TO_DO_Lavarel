<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $status = request('status', 'all');
        $baseQuery = Task::where('user_id', auth()->id());

        // Apply status filter only when a valid filter is selected.
        $tasks = Task::with('tags')
            ->where('user_id', auth()->id())
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
        $tags = Tag::orderBy('name')->get();

        return view('tasks.create', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validatedTask($request);
        $tagIds = $validated['tags'] ?? [];
        unset($validated['tags'], $validated['attachment']);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('tasks', 'public');
        }

        $task = Task::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        $task->tags()->sync($tagIds);

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
        $this->authorizeTaskOwner($task);

        $task->load('tags');
        $tags = Tag::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorizeTaskOwner($task);

        // This branch handles quick status toggles from the list page.
        if ($request->boolean('quick_status')) {
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

        $validated = $this->validatedTask($request);
        $tagIds = $validated['tags'] ?? [];
        unset($validated['tags'], $validated['attachment']);

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
        $this->authorizeTaskOwner($task);

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
        $this->authorizeTaskOwner($task);
        $this->deleteAttachment($task);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    private function validatedTask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,done'],
            'deadline' => ['required', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ], [
            'title.required' => 'Please enter a task title.',
            'status.required' => 'Please choose a task status.',
            'status.in' => 'Status must be either pending or done.',
            'deadline.required' => 'Please choose a deadline date.',
            'deadline.date' => 'Deadline must be a valid date.',
            'attachment.mimes' => 'Attachment must be a PDF, image, or Word document.',
            'attachment.max' => 'Attachment must not be larger than 5MB.',
        ]);
    }

    private function authorizeTaskOwner(Task $task): void
    {
        abort_unless($task->user_id === auth()->id(), 403);
    }

    private function deleteAttachment(Task $task): void
    {
        if ($task->attachment_path && Storage::disk('public')->exists($task->attachment_path)) {
            Storage::disk('public')->delete($task->attachment_path);
        }
    }
}
