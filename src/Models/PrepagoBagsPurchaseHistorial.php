<?php

namespace EmizorIpx\PrepagoBags\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepagoBagsPurchaseHistorial extends Model
{
    protected $table = 'prepago_bags_purchase_historial';

    protected $fillable  = ['purchase_date', 'account_id', 'number_invoices', 'number_invoices_before', 'bag_id'];


    public static function registerHistorial($data){
        self::create($data);
    }

    public static function checkPrepagoFree($accoun_id){
        return self::where('account_id', $accoun_id)->where('bag_id', 1)->exists();
    }
}
