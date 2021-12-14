<?php

namespace Idez\NovaSecurity\Models;

use Idez\NovaSecurity\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'token', 'platform', 'version', 'model', 'manufacturer', 'is_active',
    ];

    protected $table = 'devices';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the device.
     */
    public function user()
    {
        return $this->belongsTo(config('nova-security.user_model'));
    }
}
