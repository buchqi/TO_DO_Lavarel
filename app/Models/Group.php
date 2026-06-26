<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Group represents the groups database table.
// It models a collaboration space that owns many tasks and has many members.
class Group extends Model
{
    // These are the only group columns that may be mass-assigned from validated
    // controller data. owner_id is included because the controller sets it from auth().
    protected $fillable = [
        'name',
        'description',
        'owner_id',
    ];

    // A group belongs to one owner through owner_id.
    // The custom foreign key is needed because the column is owner_id, not user_id.
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Groups and users have a many-to-many member relationship.
    // The group_user pivot table stores which users joined which groups.
    public function users(): BelongsToMany
    {
        // withPivot('role') exposes the role column from the pivot table.
        // withTimestamps() keeps pivot created_at/updated_at values updated.
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    // A group has many tasks because tasks.group_id points back to groups.id.
    // This returns all tasks shared inside the group.
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // This helper turns membership rules into a readable model method.
    // Controllers use it to answer: may this user see this group?
    public function hasMember(User $user): bool
    {
        return $this->owner_id === $user->id || $this->users()->where('users.id', $user->id)->exists();
    }
}
