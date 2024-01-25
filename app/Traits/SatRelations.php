<?php

namespace App\Traits;

use Modules\Sat\Models\SatBank;
use Modules\Sat\Models\SatFiscalRegime;
use Modules\Sat\Models\SatPaymentForm;
use Modules\Sat\Models\SatUseCfdi;
use Modules\Settings\Models\PaymentDay;

trait SatRelations
{
    public function satUseCfdi()
    {
        return $this->belongsTo(SatUseCfdi::class, 'sat_use_cfdi_code', 'code')->withTrashed();
    }

    public function satFiscalRegimen()
    {
        return $this->belongsTo(SatFiscalRegime::class, 'sat_fiscal_regimen_code', 'code')->withTrashed();
    }

    public function satBank()
    {
        return $this->belongsTo(SatBank::class, 'stp_bank_code', 'code')->withTrashed();
    }

    public function getSatKindPersonTextAttribute()
    {
        return ucfirst($this->sat_kind_person);
    }

    public function satPaymentForm()
    {
        return $this->belongsTo(SatPaymentForm::class, 'sat_payment_form_code', 'code')->withTrashed();
    }

    public function paymentDay()
    {
        return $this->belongsTo(PaymentDay::class);
    }

}
