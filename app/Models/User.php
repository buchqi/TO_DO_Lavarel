<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// User represents the users database table and also participates in Laravel's
// authentication system by extending Authenticatable instead of a plain Model.
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        // The hashed cast tells Laravel to hash passwords when they are assigned.
        // This is why RegisterController can call User::create($validated)
        // without manually calling Hash::make().
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // A user has many personal or created tasks because tasks.user_id points
    // to users.id. This returns the Task models created by the user.
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // A user can own many groups. The foreign key is owner_id, so it must be
    // specified instead of Laravel's default user_id.
    public function ownedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    // A user can join many groups through the group_user pivot table.
    // This relationship returns groups where the user is a member, not owner.
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->withPivot('role')
            ->withTimestamps();
    }
}
