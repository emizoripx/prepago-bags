<?php

namespace EmizorIpx\PrepagoBags\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepagoBagsPurchaseHistorial extends Model
{
    protected $table = 'prepago_bags_purchase_historial';

    protected $fillable  = ['purchase_date', 'company_id', 'number_invoices', 'number_invoices_before', 'bag_id'];


    public static function registerHistorial($data){
        self::create($data);
    }

    public static function checkPrepagoBagGift($company_id){
        return self::where('company_id', $company_id)->where('bag_id', 1)->exists();
    }

    public static function checkPrepagoBagFree($company_id, $bag_id){
        return self::where('company_id', $company_id)->where('bag_id', $bag_id)->exists();
    }
}
