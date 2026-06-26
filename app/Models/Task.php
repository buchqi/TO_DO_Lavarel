<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

// Task represents the tasks database table.
// In MVC, this model is the data layer object controllers use when they need
// to create, read, update, or delete tasks.
class Task extends Model
{
    // Fillable fields protect mass assignment. Only these columns may be set
    // through Task::create($data) or $task->update($data).
    protected $fillable = [
        'user_id',
        'group_id',
        'title',
        'description',
        'status',
        'deadline',
        'attachment_path',
    ];

    // Casting deadline to a date gives Blade and controllers a Carbon date
    // object instead of a plain string, enabling format() and date comparison.
    protected $casts = [
        'deadline' => 'date',
    ];

    // A task belongs to one user because user_id is stored on the tasks table.
    // This returns the User who created/owns the task.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // A task may belong to one group through group_id.
    // This lets shared tasks connect to the group where collaboration happens.
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    // A task can have many tags, and each tag can belong to many tasks.
    // belongsToMany uses the task_tag pivot table to represent that connection.
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }
}
