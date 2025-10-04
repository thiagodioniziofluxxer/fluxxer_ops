<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ClientServices;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    private const ADMIN_ROUTE = 'admin.clients.index';

    public function __construct(private readonly ClientServices $clientServices)
    {
        // Middleware específico pode ser aplicado nas rotas
    }

    public function index(Request $request): Response
    {
        $this->authorizeViewAny();

        $clients = $this->clientServices->getAll($request);

        return Inertia::render('clients/index', [
            'clients' => $clients,
        ]);
    }

    public function create(): Response
    {
        $this->authorizeViewAny();

        return Inertia::render('clients/create', [
            // Adicione aqui dados auxiliares se necessário
        ]);
    }

    public function store(\App\Http\Requests\Client\CreateClientRequest $request): RedirectResponse
    {
        try {
            Gate::authorize('create', Client::class);

            $this->clientServices->create($request->validated());

            return $this->redirectWithSuccess('Cliente criado com sucesso!');
        } catch (AuthorizationException) {
            return $this->redirectWithError('Você não tem permissão para criar clientes.');
        } catch (\Exception) {
            return $this->redirectWithError('Erro ao criar cliente. Tente novamente.');
        }
    }

    public function show(string $id): Response
    {
        $client = $this->findClientOrFail($id);
        Gate::authorize('view', $client);

        return Inertia::render('clients/show', [
            'client' => $client,
        ]);
    }

    public function edit(string $id): Response
    {
        $client = $this->findClientOrFail($id);
        Gate::authorize('clientsUpdate', $client);

        return Inertia::render('clients/edit', [
            'client' => $client,
        ]);
    }

    public function update(\App\Http\Requests\Client\UpdateClientRequest $request, string $id): RedirectResponse
    {
        try {
            $client = $this->findClientOrFail($id);
            Gate::authorize('clientsUpdate', $client);

            $this->clientServices->update($id, $request->validated());

            return $this->redirectWithSuccess('Cliente atualizado com sucesso!');
        } catch (AuthorizationException) {
            return $this->redirectWithError('Você não tem permissão para atualizar este cliente.');
        } catch (\Exception) {
            return $this->redirectWithError('Erro ao atualizar cliente. Tente novamente.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $client = $this->findClientOrFail($id);
            Gate::authorize('delete', $client);

            $this->clientServices->delete($id);

            return $this->redirectWithSuccess('Cliente removido com sucesso!');
        } catch (AuthorizationException) {
            return $this->redirectWithError('Você não tem permissão para remover este cliente.');
        } catch (\Exception) {
            return $this->redirectWithError('Erro ao remover cliente. Tente novamente.');
        }
    }

    private function findClientOrFail(string $id): Client
    {
        return $this->clientServices->find($id);
    }

    private function authorizeViewAny(): void
    {
        Gate::authorize('clientsViewAny', Client::class);
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
