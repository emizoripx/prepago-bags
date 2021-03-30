<?php

namespace EmizorIpx\PrepagoBags\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function getPurchaseHistorial($company_id){
        $historial = DB::table('prepago_bags_purchase_historial')
                        ->join('prepago_bags', 'prepago_bags_purchase_historial.bag_id', '=', 'prepago_bags.id')
                        ->where('prepago_bags_purchase_historial.company_id', '=', $company_id)
                        ->select('prepago_bags.id','prepago_bags.name','prepago_bags.number_invoices','prepago_bags.frequency','prepago_bags.acumulative','prepago_bags.amount','prepago_bags_purchase_historial.number_invoices_before','prepago_bags_purchase_historial.purchase_date')
                        ->get();

        return $historial;
    }
}
