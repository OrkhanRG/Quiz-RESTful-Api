<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    public function givePermissionTo(string $permissionName): self
    {
        $permission = Permission::where('name', $permissionName)->firstOrFail();
        $this->permissions()->syncWithoutDetaching([$permission->id]);
        return $this;
    }
}
