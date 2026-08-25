<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'phone', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $keyType = 'string';

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

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function hasMenuPermission(Menu|string|int $menu, string $ability = 'read'): bool
    {
        $column = 'can_'.str_replace('can_', '', $ability);

        if (! in_array($column, ['can_read', 'can_create', 'can_update', 'can_delete'], true)) {
            return false;
        }

        $menuId = match (true) {
            $menu instanceof Menu => $menu->id,
            is_int($menu) => $menu,
            default => Menu::query()
                ->where('permission_key', $menu)
                ->where('is_active', true)
                ->value('id'),
        };

        if (! $menuId || ! Menu::query()->whereKey($menuId)->where('is_active', true)->exists()) {
            return false;
        }

        return Privilege::query()
            ->where('menu_id', $menuId)
            ->whereIn('role_id', $this->roles()->select('roles.id'))
            ->where($column, true)
            ->exists();
    }

    /** @return Collection<int, Menu> */
    public function accessibleMenus(): Collection
    {
        $menuIds = Privilege::query()
            ->whereIn('role_id', $this->roles()->select('roles.id'))
            ->where('can_read', true)
            ->pluck('menu_id')
            ->unique();

        $parentIds = Menu::query()
            ->whereIn('id', $menuIds)
            ->whereNotNull('parent_id')
            ->pluck('parent_id');

        return Menu::query()
            ->where('is_active', true)
            ->whereIn('id', $menuIds->merge($parentIds)->unique())
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN id ELSE parent_id END')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('order')
            ->get();
    }
}
