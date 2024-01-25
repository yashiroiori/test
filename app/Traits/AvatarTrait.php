<?php

namespace App\Traits;

trait AvatarTrait
{
    public function getAvatarAttribute()
    {
        if (count($this->getMedia('avatar')) > 0) {
            return $this->getMedia('avatar')[0]->getUrl();
        }
        // if(count($this->getMedia('image')) > 0){
        //     return $this->getMedia('image')[0]->getUrl();
        // }
        // if(count($this->getMedia('media')) > 0){
        //     return $this->getMedia('media')[0]->getUrl();
        // }
        return null;
    }
}
