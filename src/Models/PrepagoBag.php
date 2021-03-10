<?php

namespace EmizorIpx\PrepagoBags\Models;

use App\Utils\Traits\MakesHash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepagoBag extends Model
{

    use MakesHash;

    protected $table = 'prepago_bags';

    protected $fillable = ['number_invoices', 'name','frequency', 'acumulative', 'amount'];


    public static function getAllBags($account_id){
        if(PrepagoBagsPurchaseHistorial::checkPrepagoBagGift($account_id)){
            return self::where('id', '<>', 1)->get();
        }
        else{
            return self::all();
        }
    }
}
