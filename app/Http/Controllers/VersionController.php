<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class VersionController extends Controller
{
    public function index()
    {
        return Inertia::render('version/index', []);
    }
}
