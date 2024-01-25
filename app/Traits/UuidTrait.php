<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UuidTrait
{
    protected $isLockedUuid = true;

    /**
     * Used by Eloquent to get primary key type.
     * UUID Identified as a string.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Used by Eloquent to get if the primary key is auto increment value.
     * UUID is not.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    /**
     * Add behavior to creating and saving Eloquent events.
     *
     * @return void
     */
    public static function bootUuidTrait()
    {
        self::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}
