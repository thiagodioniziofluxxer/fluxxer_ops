<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Papel que tem acesso ao módulo de clientes.
     */
    private const ADMIN_ROLE = 'admin';

    /**
     * Verifica se o usuário é admin.
     */
    private function isAdmin(User $user): bool
    {
        return $user->role && $user->role->slug === self::ADMIN_ROLE;
    }

    /**
     * Pode listar qualquer cliente.
     */
    public function clientsViewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Pode visualizar um cliente específico.
     */
    public function clientsView(User $user, Client $client): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Pode criar clientes.
     */
    public function clientsCreate(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Pode atualizar clientes.
     */
    public function clientsUpdate(User $user, Client $client): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Pode deletar clientes.
     */
    public function clientsDelete(User $user, Client $client): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Pode restaurar clientes.
     */
    public function clientsRestore(User $user, Client $client): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Pode forçar exclusão.
     */
    public function clientsForceDelete(User $user, Client $client): bool
    {
        return $this->isAdmin($user);
    }
}
