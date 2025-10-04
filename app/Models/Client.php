<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'status'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function clientConfig()
    {
        return $this->hasOne(ClientConfig::class);
    }
}
