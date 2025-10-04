<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $latestVersion = \App\Models\Version::latest()->first();
        $versions = \App\Models\Version::orderBy('created_at', 'desc')->take(10)->get();
        $clientsCount = \App\Models\Client::count();
        $pendingDeploys = \App\Models\Deploy::where('status', 'pending')->count();
        $pendingUserLinks = \App\Models\User::where(function ($query) {
            $query->whereNull('client_id')
                ->orWhereNull('role_id');
        })->whereHas('role', function ($q) {
            $q->where('name', '!=', 'admin');
        })
            ->latest()
            ->take(50)
            ->get(['id', 'name', 'email', 'created_at']);
        return Inertia::render('dashboard/index', [
            'latestVersion' => $latestVersion,
            'versions' => $versions,
            'clientsCount' => $clientsCount,
            'pendingDeploys' => $pendingDeploys,
            'usersCount' => \App\Models\User::count(),
            'pendingUserLinks' => $pendingUserLinks,
        ]);
    }
}
