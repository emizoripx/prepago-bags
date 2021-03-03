<?php

namespace EmizorIpx\PrepagoBags\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AccountPrepagoBags extends Model
{
    protected $table = 'account_prepago_bags';

    protected $fillable = ['account_id', 'invoice_number_available', 'acumulative', 'duedate'];


    public static function getInvoiceAvailable($account_id){
        $account = self::where('account_id', $account_id)->first();
        // dd($account);
        if(is_null($account)){
            return 0;
        }
        else{
            return $account->invoice_number_available;
        }
        
    }


    
    public static function createOrUpdate($data){
        $account = self::where('account_id', $data['account_id'])->first();

        Log::debug($data);
        if(empty($account)){
            return self::create($data);
        }
        else {
            
            $account->update($data);

            return self::whereAccountId($data['account_id'])->first();
        }
    }
}
