<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SatUseCfdi extends Model
{
    use UuidTrait, SoftDeletes;

    protected $connection= 'sat';
    
    protected $fillable = [
        'code',
        'description',
        'kind_person',
        'date_start',
        'date_end',
        'receptor',
    ];

    protected $casts = [
        'code' => 'string',
        'description' => 'string',
        'kind_person' => 'string',
        'receptor' => 'array',
    ];

    protected $dates = [
        'date_start',
        'date_end',
        'created_at',
        'updated_at',
    ];
    
    protected $appends = [
        'full_name',
    ];

    public function getFullNameAttribute()
    {
        return $this->code.' - '.$this->description;
    }
    
}
