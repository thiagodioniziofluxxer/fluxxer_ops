<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determina se o usuário pode visualizar qualquer modelo.
     */
    public function usersViewAny(User $user): bool
    {
        return in_array($user->role->slug, ['admin', 'developer']);
    }

    /**
     * Determina se o usuário pode visualizar o modelo.
     */
    public function usersView(User $user, User $model): bool
    {
        return in_array($user->role->slug, ['admin', 'developer']);
    }

    public function usersCreate(User $user): bool
    {
        return in_array($user->role->slug, ['admin', 'developer']);
    }

    public function usersUpdate(User $user, User $model): bool
    {
        return $user->role->slug === 'admin' || $user->id === $model->id;
    }

    public function usersDelete(User $user, User $model): bool
    {
        return $user->role->slug === 'admin';
    }

    public function usersRestore(User $user, User $model): bool
    {
        return $user->role->slug === 'admin';
    }

    public function usersForceDelete(User $user, User $model): bool
    {
        return $user->role->slug === 'admin';
    }
}
