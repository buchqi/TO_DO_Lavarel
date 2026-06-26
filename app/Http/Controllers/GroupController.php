<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

// GroupController is the resource controller for collaborative groups.
// It demonstrates ownership, membership, route model binding, validation,
// redirects, and many-to-many pivot operations.
class GroupController extends Controller
{
    public function index()
    {
        // Called by GET /groups.
        // It receives the current browser request, uses the authenticated User
        // model relationships, and returns groups.index with two collections.
        $ownedGroups = auth()->user()
            ->ownedGroups()
            // withCount adds tasks_count without loading every Task model.
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
        // Called by GET /groups/create.
        // It returns a Blade form and does not touch the database because
        // viewing a form should be a safe, read-only request.
        return view('groups.create');
    }

    public function store(Request $request)
    {
        // Called by POST /groups.
        // It receives group form data, validates it, creates a Group model,
        // and redirects to the newly created group detail page.
        $validated = $this->validatedGroup($request);

        // owner_id is not trusted from the form. It comes from auth()->id()
        // so the logged-in user automatically becomes the group owner.
        $group = Group::create([
            ...$validated,
            'owner_id' => auth()->id(),
        ]);

        return redirect()->route('groups.show', $group)->with('success', 'Group created successfully.');
    }

    public function show(Group $group)
    {
        // Called by GET /groups/{group}.
        // Route model binding loads the Group from the URL. The method checks
        // membership, eager-loads related data, and returns groups.show.
        $this->authorizeGroupMember($group);

        $group->load([
            'owner',
            'users',
            // This nested eager-load lets the Blade table show each task's
            // creator and tags without issuing extra database queries per row.
            'tasks' => fn ($query) => $query->with(['user', 'tags'])->orderBy('deadline')->latest(),
        ]);

        return view('groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        // Called by GET /groups/{group}/edit.
        // Only the owner may edit group details, because members should not
        // be able to rename or change another user's group.
        $this->authorizeGroupOwner($group);

        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        // Called by PUT/PATCH /groups/{group}.
        // It receives the edit form data, validates it, updates the Group,
        // and redirects back to the detail page.
        $this->authorizeGroupOwner($group);

        $group->update($this->validatedGroup($request));

        return redirect()->route('groups.show', $group)->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        // Called by DELETE /groups/{group}.
        // It removes a group owned by the current user and cleans up related
        // records before redirecting to the group list.
        $this->authorizeGroupOwner($group);

        // Tasks are not deleted when a group is removed; they become personal.
        // This preserves task history while removing the collaboration container.
        $group->tasks()->update(['group_id' => null]);
        // detach() removes rows from the group_user pivot table.
        $group->users()->detach();
        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }

    public function addMember(Request $request, Group $group)
    {
        // Called by POST /groups/{group}/members.
        // It receives an email address, uses User and Group models, inserts
        // a pivot row, and redirects back with a flash message.
        $this->authorizeGroupOwner($group);

        // The exists rule proves the email belongs to a registered user before
        // the app tries to attach that user to the group.
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Please enter a member email address.',
            'email.exists' => 'No user was found with that email address.',
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        // The owner is already represented by owner_id, so adding them to the
        // members pivot would duplicate the meaning of membership.
        if ($user->id === $group->owner_id) {
            return back()->with('error', 'The owner is already part of this group.');
        }

        // This check gives a friendly error before the database unique
        // constraint would reject duplicate group_user rows.
        if ($group->users()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'This user is already a group member.');
        }

        // attach() creates a many-to-many pivot row and stores extra pivot data.
        // The role column explains how the user participates in the group.
        $group->users()->attach($user->id, ['role' => 'member']);

        return back()->with('success', 'Member added successfully.');
    }

    public function removeMember(Group $group, User $user)
    {
        // Called by DELETE /groups/{group}/members/{user}.
        // Route model binding loads both models, the owner check protects the
        // action, and detach removes only the pivot relationship.
        $this->authorizeGroupOwner($group);

        $group->users()->detach($user->id);

        return back()->with('success', 'Member removed successfully.');
    }

    private function validatedGroup(Request $request): array
    {
        // Shared validation prevents duplicated rules between store and update.
        // Laravel automatically redirects back with errors if validation fails.
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
        // Members and owners may view a group. If this guard were removed,
        // any logged-in user could guess a group id and see its tasks.
        abort_unless($group->hasMember(auth()->user()), 403);
    }

    private function authorizeGroupOwner(Group $group): void
    {
        // Only owners may update membership or group settings.
        // abort_unless returns a 403 response when the condition is false.
        abort_unless($group->owner_id === auth()->id(), 403);
    }
}
