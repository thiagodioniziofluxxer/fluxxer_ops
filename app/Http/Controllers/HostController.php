<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class HostController extends Controller
{
    public function index()
    {
        return Inertia::render('host/index', []);
    }
}
