<?php

namespace App\Services;

use Illuminate\Http\Request;

interface Service
{
    public function getAll(Request $request);

    public function find($id);

    public function store(array $data);

    public function update($id, array $data);

    public function delete($id);
}
