<?php

namespace App\Models;

use App\Traits\CreatedUpdatedAgoModelTrait;
use App\Traits\StatusModelTrait;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use UuidTrait, SoftDeletes;
    use CreatedUpdatedAgoModelTrait, StatusModelTrait;

    protected $fillable = [
        'rfc',
        'name',
        'legal_name',
        'sat_kind_person',
        'sat_fiscal_regimen_code',
        'sat_use_cfdi_code',
        'fiel_date_from',
        'fiel_date_to',
        'fiel_serial_number',
        'fiel_password',
        'fiel_validated',
        'archived',
    ];
    
    protected $casts = [
        'rfc' => 'string',
        'name' => 'string',
        'legal_name' => 'string',
        'sat_kind_person' => 'string',
        'sat_fiscal_regimen_code' => 'string',
        'sat_use_cfdi_code' => 'string',
        'fiel_date_from' => 'string',
        'fiel_date_to' => 'string',
        'fiel_serial_number' => 'string',
        'fiel_password' => 'encrypted',
        'fiel_validated' => 'boolean',
        'archived' => 'boolean',
    ];
    
    protected $appends = [
        'sat_kind_person_text',
        'can_edit',
        'is_active',
        'is_deleted',
        'is_archived',
        'status_text',
        'updated_ago',
        'created_ago',
    ];

    public static function modelData()
    {
        return [
            'titles' => [
                'index' => 'Consulta de Empresas',
                'create' => 'Nueva empresa',
                'edit' => 'Editar empresa',
            ],
            'meilisearch' => [
                'collection' => 'companies',
                'query_by' => 'name',
                'per_page' => '100',
                'facetFiltersInitial' => [
                    'filters' => '',
                    // 'toggle' => [
                    //     'is_active' => true,
                    // ],
                ],
                'facets' => [
                    'sat_kind_person_text' => [
                        'attribute' => 'sat_kind_person_text',
                        'label' => 'Tipo de solicitud',
                        'value' => 'value',
                        'type' => 'RefinementList',
                        'limit' => 10,
                        'showMoreLimit' => 300,
                    ],
                    'sat_fiscal_regimen_code_text' => [
                        'attribute' => 'sat_fiscal_regimen_code_text',
                        'label' => 'Régimen fiscal',
                        'value' => 'value',
                        'type' => 'RefinementList',
                        'limit' => 10,
                        'showMoreLimit' => 300,
                    ],
                    'sat_use_cfdi_code_text' => [
                        'attribute' => 'sat_use_cfdi_code_text',
                        'label' => 'Uso cfdi',
                        'value' => 'value',
                        'type' => 'RefinementList',
                        'limit' => 10,
                        'showMoreLimit' => 300,
                    ],
                ],
                'sort_options' => [
                    
                ],
                'config' => [
                    'searchable' => [
                        'rfc',
                        'name',
                        'legal_name',
                        'fiel_serial_number',
                    ],
                    'sortable' => [
                        'rfc',
                        'name',
                        'legal_name',
                        'fiel_serial_number',
                    ],
                    'typoTolerance' => [
                        'enabled' => true,
                        'minWordSizeForTypos' => [
                            'oneTypo' => 3,
                            'twoTypos' => 9
                        ],
                        'disableOnWords' => [],
                    ],
                    'filterable' => [
                        'sat_kind_person_text',
                        'sat_fiscal_regimen_code_text',
                        'sat_use_cfdi_code_text',
                        'is_active',
                        'is_archived',
                        'is_deleted',
                    ],
                    'ranking' => [
                        'words',
                        'typo',
                        'proximity',
                        'attribute',
                        'sort',
                        'exactness',
                        'name:asc',
                        'name:desc',
                        'rfc:asc',
                        'rfc:desc',
                    ],
                ],
            ],
            'icon' => 'building',
            'resource' => 'company',
            'actions' => [
                'browse',
                'read',
                'edit',
                'add',
                'delete',
                'force-delete',
                'archive',
                'restore',
                'import',
                'export',
            ],
            'columns_table' => [
                // [
                //     'name' => 'Avatar',
                //     'key' => 'avatar',
                //     'align' => 'center',
                //     'style' => [
                //         'width' => '85px'
                //     ],
                // ],
                [
                    'name' => 'RFC',
                    'key' => 'rfc',
                ],
                [
                    'name' => 'Nombre',
                    'key' => 'name',
                ],
                [
                    'name' => 'Tipo persona',
                    'key' => 'sat_kind_person_text',
                ],
                [
                    'name' => 'Régimen fiscal',
                    'key' => 'sat_fiscal_regimen_code_text',
                ],
                [
                    'name' => 'Uso cfdi',
                    'key' => 'sat_use_cfdi_code_text',
                ],
                [
                    'name' => 'Estatus',
                    'key' => 'status_text',
                    'column_class' => 'text-center',
                    'style' => [
                        'width' => '85px'
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = [];
        $array['id'] = $this->id;
        $array['name'] = $this->name;
        $array['rfc'] = $this->rfc;
        $array['name'] = $this->name;
        $array['legal_name'] = $this->legal_name;
        $array['sat_kind_person_text'] = $this->sat_kind_person_text;
        $array['sat_fiscal_regimen_code_text'] = $this->satFiscalRegimen->full_name ?? 'N/A';
        $array['sat_use_cfdi_code_text'] = $this->satUseCfdi->full_name ?? 'N/A';

        $array['status_text'] = $this->status_text;
        $array['status'] = $this->status;
        
        $array['created_ago'] = $this->created_ago;
        $array['created_at'] = strtotime($this->created_at);
        $array['updated_ago'] = $this->updated_ago;
        $array['updated_at'] = strtotime($this->updated_at);
        $array['deleted_ago'] = $this->deleted_ago;
        $array['deleted_at'] = strtotime($this->deleted_at);

        return $array;
    }

    public function searchableAs()
    {
        return 'companies';
    }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return true;
    }

    public function getCanEditAttribute()
    {
        return true;
    }

}
