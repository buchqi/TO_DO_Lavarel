<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Tag represents the tags database table.
// Tags classify tasks, and the same tag can be reused by many tasks.
class Tag extends Model
{
    // Only the tag name is mass assignable because tags are simple labels.
    protected $fillable = [
        'name',
    ];

    // A tag belongs to many tasks through the task_tag pivot table.
    // This returns every Task that has been assigned this tag.
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_tag');
    }
}
