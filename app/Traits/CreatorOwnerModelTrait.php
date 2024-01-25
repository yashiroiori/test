<?php

namespace App\Traits;

trait CreatorOwnerModelTrait
{
    public function creator($model)
    {
        return $this->morphMany($model, 'creatorable');
    }

    public function owner($model)
    {
        return $this->morphMany($model, 'ownerable');
    }

    public function ownerable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function creatorable()
    {
        return $this->morphTo()->withTrashed();
    }
}
