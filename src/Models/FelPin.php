<?php

namespace EmizorIpx\PrepagoBags\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FelPin extends Model
{
    use HasFactory;

    protected $table = 'fel_pines';

     protected $guarded = [];


     public function validateDueDatePin(){
         $now = Carbon::now();

         return $now->lessThanOrEqualTo($this->due_date_pin);
     }


     public function validateUsageDate(){

        return is_null($this->usage_date);

     }

     public function generarPin(){

        do {
            $newPin = rand(1111,9999)."-".time()."-".rand(1111,9999);
        } while ($this->validate($newPin));

        return $newPin;

     }

     public function validate($pin){
         return self::where('pin', $pin)->exists();
     }


}
