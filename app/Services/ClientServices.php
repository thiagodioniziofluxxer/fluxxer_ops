<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientServices implements Service
{
    public function __construct(private Client $clientModel)
    {
    }

    public function getAll(Request $request)
    {
        $query = $this->clientModel->newQuery();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter') && $this->hasColumn('status')) {
            $filter = $request->input('filter');
            $query->where('status', $filter);

        }

        return $query->paginate();
    }

    public function find($id)
    {
        return $this->clientModel->findOrFail($id);
    }

    public function store(array $data)
    {
        $clientData = $this->filterData($data);
        return $this->clientModel->create($clientData);
    }

    public function update($id, array $data)
    {
        $client = $this->clientModel->findOrFail($id);
        $clientData = $this->filterData($data);
        $client->update($clientData);
        return $client;
    }

    public function delete($id): bool
    {
        $client = $this->clientModel->findOrFail($id);
        $client->delete();
        return true;
    }

    private function filterData(array $data): array
    {
        $allowed = $this->clientModel->getFillable();
        if (empty($allowed)) {
            return collect($data)->only(['name'])->toArray();
        }
        return collect($data)->only($allowed)->toArray();
    }

    private function hasColumn(string $column): bool
    {
        static $cache = [];
        $table = $this->clientModel->getTable();

        if (!isset($cache[$table])) {
            try {
                $cache[$table] = \Schema::getColumnListing($table);
            } catch (\Throwable) {
                $cache[$table] = [];
            }
        }

        return in_array($column, $cache[$table], true);
    }
}
