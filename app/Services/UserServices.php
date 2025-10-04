<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;

class UserServices implements Service
{
    public function __construct(private User $userModel) {}

    public function getAll(Request $request)
    {
        $query = $this->userModel->query()->with('role', 'client');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }

        return $query->paginate();
    }

    public function find($id)
    {
        return $this->userModel->findOrFail($id);
    }

    public function store(array $data)
    {
        $userData = $this->filterUserData($data);

        if (isset($userData['password'])) {
            $userData['password'] = bcrypt($userData['password']);
        }

        return $this->userModel->create($userData);
    }

    public function update($id, array $data)
    {
        $user = $this->userModel->findOrFail($id);

        $userData = $this->filterUserData($data);

        if (isset($userData['password'])) {
            $userData['password'] = bcrypt($userData['password']);
        } else {
            unset($userData['password']);
        }

        $user->update($userData);

        return $user;
    }

    public function delete($id): bool
    {
        $user = $this->userModel->findOrFail($id);
        $user->delete();

        return true;
    }

    private function filterUserData(array $data): array
    {
        return collect($data)->only([
            'name',
            'email',
            'password',
            'client_id',
            'role_id',
        ])->toArray();
    }
}
