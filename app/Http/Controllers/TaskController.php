<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $status = request('status', 'all');

        // Apply status filter only when a valid filter is selected.
        $tasks = Task::when(in_array($status, ['pending', 'done'], true), function ($query) use ($status) {
            $query->where('status', $status);
        })
            ->orderBy('deadline')
            ->latest()
            ->get();

        return view('tasks.index', compact('tasks', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,done'],
            'deadline' => ['required', 'date'],
        ], [
            'title.required' => 'Please enter a task title.',
            'status.required' => 'Please choose a task status.',
            'status.in' => 'Status must be either pending or done.',
            'deadline.required' => 'Please choose a deadline date.',
            'deadline.date' => 'Deadline must be a valid date.',
        ]);

        Task::create($validated);

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
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
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

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,done'],
            'deadline' => ['required', 'date'],
        ], [
            'title.required' => 'Please enter a task title.',
            'status.required' => 'Please choose a task status.',
            'status.in' => 'Status must be either pending or done.',
            'deadline.required' => 'Please choose a deadline date.',
            'deadline.date' => 'Deadline must be a valid date.',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
