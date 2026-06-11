<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'user';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public function getRoleAttribute($value)
    {
        return strtolower($value);
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = strtoupper($value);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    public function technician()
    {
        return $this->hasOne(Technician::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTechnician()
    {
        return $this->role === 'technician';
    }

    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $permissionName): bool
    {
        if (!$this->role_id) {
            return false;
        }
        $role = $this->roleRelation;
        if (!$role) {
            return false;
        }
        if ($role->name === 'Admin' || $role->name === 'Super Admin') {
            return true;
        }
        return $role->permissions()->where('name', $permissionName)->exists();
    }

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
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
