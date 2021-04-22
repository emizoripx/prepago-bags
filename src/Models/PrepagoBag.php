<?php

namespace EmizorIpx\PrepagoBags\Models;

use App\Utils\Traits\MakesHash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PrepagoBag extends Model
{

    use MakesHash;

    protected $table = 'prepago_bags';

    protected $fillable = ['number_invoices', 'name','frequency', 'acumulative', 'amount'];


    public static function getAllBags($company_id){
        
        $bags = DB::select("SELECT * FROM prepago_bags WHERE id NOT IN (SELECT DISTINCT prepago_bags_purchase_historial.bag_id FROM prepago_bags_purchase_historial WHERE prepago_bags_purchase_historial.company_id = $company_id)
        UNION (SELECT * FROM prepago_bags WHERE amount > 0)");
            
        
        return $bags;
        
    }
}
