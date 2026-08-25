<?php

namespace App\Concerns;

trait AuthorizesMenuPermission
{
    protected function authorizeMenuPermission(string $permissionKey, string $ability): void
    {
        abort_unless(auth()->user()?->hasMenuPermission($permissionKey, $ability), 403);
    }
}
