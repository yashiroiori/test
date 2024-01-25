<?php

namespace App\Jobs;

use App\Models\Cfdi;
use App\Models\SatPaymentForm;
use App\Models\SatUseCfdi;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelWriter;

class CfdiCreateFileExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $params,$user,$file_type,$fields;

    const FIELDS = [
        "period" => "Periodo" ,
        "version" => "Versión" ,
        "uuid" => "UUID" ,
        "uuids_related" => "UUIDs relacionados" ,
        "relationship_type" => "Tipo relacion" ,
        "transmitter_zip" => "CP Expedición" ,
        "serie" => "Serie" ,
        "folio" => "Folio" ,
        "voucher_type" => "Tipo" ,
        "date_emit" => "Fecha emisión" ,
        "date_stamp" => "Fecha certificación" ,
        "pac_certification" => "PAC Certificación" ,
        "transmitter_rfc" => "RFC emisor" ,
        "transmitter_regimen" => "Regimen emisor" ,
        "transmitter_name" => "Razón social emisor" ,
        "receiver_rfc" => "RFC receptor" ,
        "receiver_name" => "Razón social receptor" ,
        "receiver_regimen" => "Regimen receptor" ,
        "receiver_zip" => "Domicilio receptor" ,
        "product_keys" => "Claves de productos" ,
        "concepts" => "Concepto" ,
        "receiver_sat_use_cfdi" => "Uso Cfdi" ,
        "complements" => "Complementos" ,
        "global_periodicity" => "Global periodicidad" ,
        "global_months" => "Global meses" ,
        "global_years" => "Global año" ,
        "effect" => "Efecto" ,
        "sat_status" => "Estado" ,
        "date_cancel" => "Fecha proceso cancelación" ,
        "cancellation_status" => "Estado cancelación" ,
        "cancellation_process_status" => "Estado proceso cancelación" ,
        "cancellation_reason" => "Motivo cancelación" ,
        "cancellation_folio" => "Folio sustitución cancelación" ,
        "currency" => "Moneda" ,
        "exchange_rate" => "Tipo de cambio" ,
        "exportation" => "Exportación" ,
        "payment_method" => "Método de pago" ,
        "sat_payment_form_code" => "Forma de pago" ,
        "subtotal" => "Subtotal" ,
        "discount" => "Descuento" ,
        "tax_transferred" => "IVA trasladado" ,
        "tax_exempt" => "IVA extento" ,
        "tax_withheld" => "IVA retenido" ,
        "isr_withheld" => "ISR retenido" ,
        "ieps_transferred" => "IEPS trasladado" ,
        "ieps_withheld" => "IEPS retenido" ,
        "retained_premises" => "Local retenido" ,
        "moved_premises" => "Local trasladado" ,
        "total" => "Total",
    ];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_type,$params = [], $user, $fields = [])
    {
        $this->params = $params;
        $this->user = $user;
        $this->file_type = $file_type;
        $this->fields = $fields;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_name = Str::uuid().'.'.$this->file_type;
        $query = Cfdi::query()->with('company');
        if(isset($this->params['query'])){
            $query->where(function($query){
                return $query->where('receiver_name','LIKE','%'.$this->params['query'].'%')
                            ->orWhere('folio','LIKE','%'.$this->params['query'].'%')
                            ->orWhere('total','LIKE','%'.$this->params['query'].'%');
            });
        }
        if(isset($this->params['refinementList'])){
            foreach($this->params['refinementList'] AS $field=>$values){
                switch($field){
                    case 'sat_payment_form_code':
                        foreach($values AS $value){
                            $satPaymentForm = SatPaymentForm::query()->get()->where('full_name',$value)->first();
                            if($satPaymentForm){
                                $query->where($field,$satPaymentForm->code);
                            }
                        }
                    break;
                    case 'payment_method':
                        foreach($values AS $value){
                            $query->where($field,$value == 'N/A' ? '' : $value);
                        }
                    break;
                    case 'receiver_sat_use_cfdi':
                        foreach($values AS $value){
                            $satPaymentForm = SatUseCfdi::query()->get()->where('full_name',$value)->first();
                            if($satPaymentForm){
                                $query->where($field,$satPaymentForm->code);
                            }
                        }
                    break;
                    case 'voucher_type_text':
                        foreach($values AS $value){
                            $query->where('voucher_type',$value[0]);
                        }
                    break;
                    case 'date_stamp':
                    case 'date_payment':
                    case 'date_payment_init':
                    case 'date_payment_end':
                        foreach($values AS $value){
                            $query->where($field,'LIKE',$value.'%');
                        }
                    break;
                    case 'sat_status':
                        foreach($values AS $value){
                            $query->where($field,$value == 'N/A' ? null : ($value == 'Activo' ? 1 : 0));
                        }
                    break;
                    default:
                        $query->whereIn($field,$values);
                    break;
                }
            }
        }
        if(isset($this->params['configure']['filters'])){
            $fields = explode(',',$this->params['configure']['filters']);
            foreach($fields AS $field){
                list($field,$value) = explode('=',$field);
                $query->where($field,$value);
            }
        }
        // $rows = [];
        // $query->get()->each(function ($cfdi) use (&$rows) {
        //     $rows[] = collect($this->fields)->map(function($field)use($cfdi){
        //         if(str_starts_with($field,'date_')){
        //             return Carbon::parse($cfdi->{$field})->format('Y-m-d H:i:s');
        //         }
        //         switch($field){
        //             case 'concepts':
        //                 return is_array($cfdi->concepts) ? implode(', ',$cfdi->concepts) : '';
        //             case 'product_keys':
        //                 return is_array($cfdi->product_keys) ? implode(', ',$cfdi->product_keys) : '';
        //             case 'uuids_related':
        //                 return is_array($cfdi->uuids_related) ? implode(', ',$cfdi->uuids_related) : '';
        //             case 'sat_payment_form_code':
        //                 return $cfdi->satPaymentForm->full_name ?? 'N/A';
        //             case 'receiver_sat_use_cfdi':
        //                 return $cfdi->satUseCfdi->full_name ?? '';
        //             case 'voucher_type':
        //                 return $cfdi->voucher_type_text;
        //             case 'payment_method':
        //                 return $cfdi->getPaymentMethod();
        //             case 'sat_status':
        //                 return $cfdi->sat_status == true ? 'Activo' : 'Cancelado';
        //             default:
        //                 return $cfdi->{$field};
        //         }
        //     })->toArray();
        // });
        $writer = SimpleExcelWriter::create(
                file: Storage::disk('export_tmp')->path($file_name),
                // configureWriter:  function ($writer) {
                //     $options = $writer->getOptions();
                //     if($this->file_type == 'xlsx'){
                //         $options->setColumnWidth(30,3,4,5,8,9,10,11,12,13,16,17,18,19);
                //         $options->setColumnWidth(50,1,2,6,7,14,15,);
                //     }
                //     // $options->setColumnWidth(250,20);
                // },
            )
            ->addHeader(collect($this->fields)->map(function($field){
                return $this::FIELDS[$field];
            })->toArray());
        $n = 0;
        $query->chunk(500, function($cfdis)use($writer) {
            foreach($cfdis as $cfdi) {
                $writer->addRow(collect($this->fields)->map(function($field)use($cfdi){
                    if(str_starts_with($field,'date_')){
                        return Carbon::parse($cfdi->{$field})->format('Y-m-d H:i:s');
                    }
                    switch($field){
                        case 'concepts':
                            return is_array($cfdi->concepts) ? implode(', ',$cfdi->concepts) : '';
                        case 'product_keys':
                            return is_array($cfdi->product_keys) ? implode(', ',$cfdi->product_keys) : '';
                        case 'uuids_related':
                            return is_array($cfdi->uuids_related) ? implode(', ',$cfdi->uuids_related) : '';
                        case 'sat_payment_form_code':
                            return $cfdi->satPaymentForm->full_name ?? 'N/A';
                        case 'receiver_sat_use_cfdi':
                            return $cfdi->satUseCfdi->full_name ?? '';
                        case 'voucher_type':
                            return $cfdi->voucher_type_text;
                        case 'payment_method':
                            return $cfdi->getPaymentMethod();
                        case 'sat_status':
                            return $cfdi->sat_status == true ? 'Activo' : 'Cancelado';
                        default:
                            return $cfdi->{$field};
                    }
                })->toArray());
            }
            flush();
        });
        // $query->get()->each(function ($cfdi) use ($writer,&$n) {
        //     $writer->addRow(collect($this->fields)->map(function($field)use($cfdi){
        //         if(str_starts_with($field,'date_')){
        //             return Carbon::parse($cfdi->{$field})->format('Y-m-d H:i:s');
        //         }
        //         switch($field){
        //             case 'concepts':
        //                 return is_array($cfdi->concepts) ? implode(', ',$cfdi->concepts) : '';
        //             case 'product_keys':
        //                 return is_array($cfdi->product_keys) ? implode(', ',$cfdi->product_keys) : '';
        //             case 'uuids_related':
        //                 return is_array($cfdi->uuids_related) ? implode(', ',$cfdi->uuids_related) : '';
        //             case 'sat_payment_form_code':
        //                 return $cfdi->satPaymentForm->full_name ?? 'N/A';
        //             case 'receiver_sat_use_cfdi':
        //                 return $cfdi->satUseCfdi->full_name ?? '';
        //             case 'voucher_type':
        //                 return $cfdi->voucher_type_text;
        //             case 'payment_method':
        //                 return $cfdi->getPaymentMethod();
        //             case 'sat_status':
        //                 return $cfdi->sat_status == true ? 'Activo' : 'Cancelado';
        //             default:
        //                 return $cfdi->{$field};
        //         }
        //     })->toArray());
        //     if ($n % 1000 === 0) {
        //         \Log::info($n);
        //         flush(); // Flush the buffer every 1000 rows
        //     }
        //     $n++;
        // });
        $writer->close();
    }
}
