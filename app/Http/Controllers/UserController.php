<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use App\Services\UserServices;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    private const ADMIN_ROUTE = 'admin.users.index';

    public function __construct(private readonly UserServices $userServices)
    {
        // Middleware deve ser aplicado nas rotas ou em middlewares separados
    }

    public function index(Request $request): Response
    {
        $this->authorizeViewAny();

        $users = $this->userServices->getAll($request);

        return Inertia::render('users/index', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        $this->authorizeViewAny();

        return Inertia::render('users/create', [
            'roles' => $this->getAllRoles(),
            'clients' => $this->getAllClients(),
        ]);
    }

    public function store(\App\Http\Requests\User\CreateUserRequest $request): RedirectResponse
    {
        try {
            Gate::authorize('create', User::class);

            $this->userServices->create($request->validated());

            return $this->redirectWithSuccess('Usuário criado com sucesso!');
        } catch (AuthorizationException) {
            return $this->redirectWithError('Você não tem permissão para criar usuários.');
        } catch (\Exception) {
            return $this->redirectWithError('Erro ao criar usuário. Tente novamente.');
        }
    }

    public function show(string $id): Response
    {
        $user = $this->findUserOrFail($id);
        Gate::authorize('view', $user);

        return Inertia::render('users/show', [
            'user' => $user,
        ]);
    }

    public function edit(string $id): Response
    {
        $user = $this->findUserOrFail($id);
        Gate::authorize('usersUpdate', $user);

        return Inertia::render('users/edit', [
            'user' => $user,
            'roles' => $this->getAllRoles(),
            'clients' => $this->getAllClients(),
        ]);
    }

    public function update(\App\Http\Requests\User\UpdateUserRequest $request, string $id): RedirectResponse
    {
        try {
            $user = $this->findUserOrFail($id);
            Gate::authorize('usersUpdate', $user);

            $this->userServices->update($id, $request->validated());

            return $this->redirectWithSuccess('Usuário atualizado com sucesso!');
        } catch (AuthorizationException) {
            return $this->redirectWithError('Você não tem permissão para atualizar este usuário.');
        } catch (\Exception) {
            return $this->redirectWithError('Erro ao atualizar usuário. Tente novamente.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $user = $this->findUserOrFail($id);
            Gate::authorize('delete', $user);

            $this->userServices->delete($id);

            return $this->redirectWithSuccess('Usuário removido com sucesso!');
        } catch (AuthorizationException) {
            return $this->redirectWithError('Você não tem permissão para remover este usuário.');
        } catch (\Exception) {
            return $this->redirectWithError('Erro ao remover usuário. Tente novamente.');
        }
    }

    private function findUserOrFail(string $id): User
    {
        return $this->userServices->find($id);
    }

    private function authorizeViewAny(): void
    {
        Gate::authorize('usersViewAny', User::class);
    }

    private function getAllRoles(): \Illuminate\Database\Eloquent\Collection
    {
        return Role::all();
    }

    private function getAllClients(): \Illuminate\Database\Eloquent\Collection
    {
        return Client::all();
    }

    private function redirectWithSuccess(string $message): RedirectResponse
    {
        return redirect()
            ->route(self::ADMIN_ROUTE)
            ->with('success', $message);
    }

    private function redirectWithError(string $message): RedirectResponse
    {
        return redirect()
            ->route(self::ADMIN_ROUTE)
            ->with('error', $message);
    }
}
