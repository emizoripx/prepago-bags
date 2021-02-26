<?php

namespace EmizorIpx\PrepagoBags\Models;

use App\Utils\Traits\MakesHash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepagoBag extends Model
{

    use MakesHash;

    protected $table = 'prepago_bags';

    protected $fillable = ['number_invoices', 'name','frequency', 'acumulative'];


    public static function getAllBags(){
        return self::all();
    }
}
