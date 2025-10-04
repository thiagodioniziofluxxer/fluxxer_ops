<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientConfig extends Model
{
    protected $fillable = [
        'client_id',
        'host_id',
        'config_key',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
    ];

    /**
     * Obtém o cliente relacionado.
     */
    public function client() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
}

    /**
     * Obtém o host relacionado.
     */
    public function host(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Host::class);
    }
}
