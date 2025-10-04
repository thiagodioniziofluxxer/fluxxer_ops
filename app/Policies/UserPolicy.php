<?php

     namespace App\Policies;

     use App\Models\User;

     class UserPolicy
     {
         /**
          * Papéis permitidos para ações administrativas.
          */
         private const ADMIN_ROLES = ['admin', 'developer'];

         /**
          * Verifica se o usuário possui um dos papéis permitidos.
          */
         private function hasRole(User $user, array $roles): bool
         {
             return in_array($user->role->slug, $roles, true);
         }

         /**
          * Determina se o usuário pode visualizar qualquer usuário.
          */
         public function usersViewAny(User $user): bool
         {
             return $this->hasRole($user, self::ADMIN_ROLES);
         }

         /**
          * Determina se o usuário pode visualizar um usuário específico.
          */
         public function usersView(User $user, User $model): bool
         {
             return $this->hasRole($user, self::ADMIN_ROLES);
         }

         /**
          * Determina se o usuário pode criar usuários.
          */
         public function usersCreate(User $user): bool
         {
             return $this->hasRole($user, self::ADMIN_ROLES);
         }

         /**
          * Determina se o usuário pode atualizar um usuário.
          */
         public function usersUpdate(User $user, User $model): bool
         {
             return $user->role->slug === 'admin' || $user->id === $model->id;
         }

         /**
          * Determina se o usuário pode deletar um usuário.
          */
         public function usersDelete(User $user, User $model): bool
         {
             return $user->role->slug === 'admin';
         }

         /**
          * Determina se o usuário pode restaurar um usuário.
          */
         public function usersRestore(User $user, User $model): bool
         {
             return $user->role->slug === 'admin';
         }

         /**
          * Determina se o usuário pode forçar a exclusão de um usuário.
          */
         public function usersForceDelete(User $user, User $model): bool
         {
             return $user->role->slug === 'admin';
         }
     }
