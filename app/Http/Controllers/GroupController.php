<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $ownedGroups = auth()->user()
            ->ownedGroups()
            ->withCount('tasks')
            ->latest()
            ->get();

        $memberGroups = auth()->user()
            ->groups()
            ->with('owner')
            ->withCount('tasks')
            ->latest()
            ->get();

        return view('groups.index', compact('ownedGroups', 'memberGroups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatedGroup($request);

        $group = Group::create([
            ...$validated,
            'owner_id' => auth()->id(),
        ]);

        return redirect()->route('groups.show', $group)->with('success', 'Group created successfully.');
    }

    public function show(Group $group)
    {
        $this->authorizeGroupMember($group);

        $group->load([
            'owner',
            'users',
            'tasks' => fn ($query) => $query->with(['user', 'tags'])->orderBy('deadline')->latest(),
        ]);

        return view('groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        $this->authorizeGroupOwner($group);

        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorizeGroupOwner($group);

        $group->update($this->validatedGroup($request));

        return redirect()->route('groups.show', $group)->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        $this->authorizeGroupOwner($group);

        $group->tasks()->update(['group_id' => null]);
        $group->users()->detach();
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }

    public function addMember(Request $request, Group $group)
    {
        $this->authorizeGroupOwner($group);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Please enter a member email address.',
            'email.exists' => 'No user was found with that email address.',
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        if ($user->id === $group->owner_id) {
            return back()->with('error', 'The owner is already part of this group.');
        }

        if ($group->users()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'This user is already a group member.');
        }

        $group->users()->attach($user->id, ['role' => 'member']);

        return back()->with('success', 'Member added successfully.');
    }

    public function removeMember(Group $group, User $user)
    {
        $this->authorizeGroupOwner($group);

        $group->users()->detach($user->id);

        return back()->with('success', 'Member removed successfully.');
    }

    private function validatedGroup(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Please enter a group name.',
            'name.max' => 'Group name must not be longer than 255 characters.',
        ]);
    }

    private function authorizeGroupMember(Group $group): void
    {
        abort_unless($group->hasMember(auth()->user()), 403);
    }

    private function authorizeGroupOwner(Group $group): void
    {
        abort_unless($group->owner_id === auth()->id(), 403);
    }
}
