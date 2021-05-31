<?php

namespace EmizorIpx\PrepagoBags\Models;
use Illuminate\Database\Eloquent\Model;
use App\Utils\Traits\MakesHash;
use Carbon\Carbon;

class PrepagoBagsPayment extends Model
{

    protected $table = 'prepago_bags_payments';

    protected $guarded = [];


    // protected static function boot()
    // {
    //     parent::boot();
    //     static::created(function ($query) {
    //         // remove unused qr payment register 
    //         \DB::table('prepago_bags_payments')
    //         ->whereNull('paid_on')
    //         ->whereRaw(" updated_at < now() - interval 2 DAY")
    //         ->delete();
    //     });
    // }


    public static function generateId($companyId,$prepagoBagId, $amount) 
    {
        $new_generated =self::create([
            'prepago_bag_id' => $prepagoBagId,
            'company_id' => $companyId,
            'amount' => $amount,
            'created_at' => Carbon::now()
        ]);

        return $new_generated->id;
    }

    public function company()
    {
        return $this->hasOne(AccountPrepagoBags::class,"id","company_id"); 
    }
}