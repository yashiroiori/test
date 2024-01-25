<?php

namespace App\Models;

use App\Traits\CreatedUpdatedAgoModelTrait;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cfdi extends Model
{
    use UuidTrait, SoftDeletes;
    use CreatedUpdatedAgoModelTrait;

    protected $fillable = [
        'uuid',
        'version',
        'folio',
        'serie',
        'date_stamp',
        'sat_payment_form_code',
        'certificate_number',
        'currency',
        'subtotal',
        'total',
        'voucher_type',
        'payment_method',
        'transmitter_zip',
        'transmitter_rfc',
        'transmitter_name',
        'transmitter_regimen',
        'receiver_rfc',
        'receiver_name',
        'receiver_sat_use_cfdi',
        'sat_status',
        'sat_status_cancel',
        'date_cancel',
        'metadata',
        'has_xml',
        'xml',
        'updated_at',
        'created_at',

        'period',
        'uuids_related',
        'relationship_type',
        'date_emit',
        'pac_certification',
        'receiver_regimen',
        'receiver_zip',
        'product_keys',
        'concepts',
        'complements',
        'global_periodicity',
        'global_months',
        'global_years',
        'effect',
        'date_cancellation_process',
        'cancellation_status',
        'cancellation_process_status',
        'cancellation_reason',
        'cancellation_folio',
        'exchange_rate',
        'exportation',
        'discount',
        'tax_transferred',
        'tax_exempt',
        'tax_withheld',
        'isr_withheld',
        'ieps_transferred',
        'ieps_withheld',
        'retained_premises',
        'moved_premises',
    ];

    protected $casts = [
        'uuid' => 'string',
        'version' => 'string',
        'folio' => 'string',
        'serie' => 'string',
        'sat_payment_form_code' => 'string',
        'certificate_number' => 'string',
        'currency' => 'string',
        'subtotal' => 'float',
        'total' => 'float',
        'voucher_type' => 'string',
        'payment_method' => 'string',
        'transmitter_zip' => 'string',
        'transmitter_rfc' => 'string',
        'transmitter_name' => 'string',
        'transmitter_regimen' => 'string',
        'receiver_rfc' => 'string',
        'receiver_name' => 'string',
        'receiver_sat_use_cfdi' => 'string',
        'sat_status' => 'string',
        'sat_status_cancel' => 'string',
        'date_cancel' => 'datetime:Y-m-d H:i:s',
        'metadata' => 'array',
        'has_xml' => 'boolean',
        'xml' => 'string',
        'date_stamp' => 'datetime:Y-m-d H:i:s',

        'period' => 'string',
        'relationship_type' => 'string',
        'uuids_related' => 'array',
        'date_emit' => 'datetime:Y-m-d H:i:s',
        'pac_certification' => 'string',
        'receiver_regimen' => 'string',
        'receiver_zip' => 'string',
        'product_keys' => 'array',
        'concepts' => 'array',
        'complements' => 'string',
        'global_periodicity' => 'string',
        'global_months' => 'string',
        'global_years' => 'string',
        'effect' => 'string',
        'date_cancellation_process' => 'datetime:Y-m-d H:i:s',
        'cancellation_status' => 'string',
        'cancellation_process_status' => 'string',
        'cancellation_reason' => 'string',
        'cancellation_folio' => 'string',
        'exchange_rate' => 'float',
        'exportation' => 'string',
        'discount' => 'float',
        'tax_transferred' => 'float',
        'tax_exempt' => 'float',
        'tax_withheld' => 'float',
        'isr_withheld' => 'float',
        'ieps_transferred' => 'float',
        'ieps_withheld' => 'float',
        'retained_premises' => 'float',
        'moved_premises' => 'float',
    ];

    protected $dates = [
        'date_emit',
        'date_stamp',
        'date_payment',
        'date_payment_init',
        'date_payment_end',
        'deleted_at',
        'updated_at',
        'created_at',
    ];

    protected $appends = [
        'full_folio',
        'voucher_type_text',
        'updated_ago',
        'created_ago',
    ];

    
    public function satFiscalRegimenCodeTransmitter()
    {
        return $this->belongsTo(SatFiscalRegime::class, 'transmitter_regimen', 'code')->withTrashed();
    }

    public function satUseCfdi()
    {
        return $this->belongsTo(SatUseCfdi::class, 'receiver_sat_use_cfdi', 'code')->withTrashed();
    }

    public function satPaymentForm()
    {
        return $this->belongsTo(SatPaymentForm::class, 'sat_payment_form_code', 'code')->withTrashed();
    }

    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    public function getPaymentMethod()
    {
        switch($this->payment_method){
            case 'PPD':
                return 'PPD';
            case 'PUE':
                return 'PUE';
            case 99:
                return '99';
        }
        return 'N/A';
    }

    public function getVoucherTypeTextAttribute()
    {
        switch($this->voucher_type){
            case 'N':
                return 'Nómina';
            break;
            case 'P':
                return 'Pago';
            break;
            case 'I':
                return 'Ingreso';
            break;
            case 'E':
                return 'Egreso';
            break;
        }
        return 'N/A';
    }

    public function getFullFolioAttribute()
    {
        return fullDataFromArray([
            $this->serie,
            $this->folio,
        ], '-');
    }

}
