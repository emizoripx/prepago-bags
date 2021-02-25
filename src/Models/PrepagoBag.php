<?php

namespace EmizorIpx\PrepagoBags\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepagoBag extends Model
{
    protected $table = 'prepago_bags';

    protected $fillable = ['number_invoices', 'name','frequency', 'acumulative'];
}
