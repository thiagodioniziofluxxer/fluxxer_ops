<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Host extends Model
{
    protected $fillable = [
        'ip',
        'credentials'
    ];

    protected $casts = [
        'credentials' => 'array',
    ];

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function clients()
    {
        return $this->hasManyThrough(
            Client::class,
            ClientConfig::class,
            'host_id',   // Foreign key on ClientConfig
            'id',        // Foreign key on Client
            'id',        // Local key on Host
            'client_id'  // Local key on ClientConfig
        );
    }
}
