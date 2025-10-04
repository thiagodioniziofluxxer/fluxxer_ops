<?php

namespace App\Http\Controllers;

use App\Models\Deploy;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DeployController extends Controller
{

    public function __construct()
    {
       Gate::authorize('deploy-view-any',Deploy::class);
    }

    public function index()
    {
        return Inertia::render('deploy/index', []);
    }
}
