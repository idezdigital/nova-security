<?php

namespace Idez\NovaSecurity\Traits;

use Illuminate\Support\Str;

/**
 * @method static creating(\Closure $param)
 */
trait HasUuid
{
    public static function bootHasUuid()
    {

        static::creating(function ($model) {
            if (blank($model->api_key)) {
                $model->api_key = Str::uuid();
            }

            if (blank($model->api_secret)) {
                $model->api_secret = Str::uuid();
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
