<?php

namespace EmizorIpx\PrepagoBags\Models;

use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AccountPrepagoBags extends Model
{
    protected $table = 'account_prepago_bags';

    protected $fillable = ['company_id', 'invoice_number_available', 'acumulative', 'duedate', 'production', 'delete', 'deleted_at', 'is_postpago', 'invoice_counter', 'enabled', 'phase'];


    public static function getInvoiceAvailable($company_id){
        $account = self::where('company_id', $company_id)->first();
        // dd($account);
        if(is_null($account)){
            return 0;
        }
        else{
            return $account->invoice_number_available;
        }
        
    }

    
    
    public static function createOrUpdate($data){
        $account = self::where('company_id', $data['company_id'])->first();

        Log::debug($data);
        if(empty($account)){
            return self::create($data);
        }
        else {
            
            $account->update($data);

            return self::whereCompanyId($data['company_id'])->first();
        }
    }

    public function checkBagDuedate(){
        $dateNow = Carbon::now();

        if($dateNow->greaterThan($this->duedate)){
            throw new PrepagoBagsException("Su bolsa prepago ha expirado");
        }
    }

    public function checkInvoiceAvailable(){
        if($this->invoice_number_available <=  0){
            throw new PrepagoBagsException("Facturas no diponibles para emitir. Adquirir un nuevo plan.");
        }
    }


    public function addNumberInvoice(){
        $this->invoice_number_available = $this->invoice_number_available + 1;
        \Log::debug('Sumar numero de Factura');
        \Log::debug($this->invoice_number_available);
        return $this;
    }

    public function reduceNumberInvoice(){
        $this->invoice_number_available = $this->invoice_number_available - 1;
        \Log::debug('Restar numero de Factura');
        \Log::debug($this->invoice_number_available);
        return $this;
    }

    public function checkIsPostpago(){
        return $this->is_postpago;
    }

    public function getAccountTypeBadgeAttribute()
    {
        $color = "success";
        $name = "PREPAGO";
        if ($this->is_postpago) {
            $color = "info";
            $name = "POSTPAGO";
        }

        return "<span class='badge badge-$color'>$name</span>";
    }

    
}
