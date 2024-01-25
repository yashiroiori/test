<?php

namespace App\Traits;

trait StatusModelTrait
{
    public function scopeActive($query)
    {
        return $query->where('archived', false)->where('deleted_at', null);
    }

    public function scopeArchived($query)
    {
        return $query->where('archived', true)->where('deleted_at', null);
    }

    public function isActive()
    {
        return ! $this->isArchived() && ! $this->isDeleted();
    }

    public function isArchived()
    {
        return (bool) $this->archived;
    }

    public function isDeleted()
    {
        if ($this->deleted_at != null) {
            return true;
        }
        return false;
    }

    public function isCanceled()
    {
        return $this->state == $this::STATUS_CANCEL;
    }

    public function getStatusTextAttribute()
    {
        if ($this->isDeleted()) {
            return [
                'color' => 'danger',
                'text' => __('admin.status.delete'),
            ];
        } elseif ($this->isArchived()) {
            return [
                'color' => 'warning',
                'text' => __('admin.status.archive'),
            ];
        }
        return [
            'color' => 'success',
            'text' => __('admin.status.active'),
        ];
    }

    public function getIsActiveAttribute()
    {
        return $this->isActive();
    }

    public function getIsArchivedAttribute()
    {
        return $this->isArchived();
    }

    public function getIsDeletedAttribute()
    {
        return $this->isDeleted();
    }

    public function getIsCanceledAttribute()
    {
        return $this->isCanceled();
    }
}
