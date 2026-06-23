<?php

namespace EmizorIpx\PrepagoBags\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyStatistic extends Model
{
    use HasFactory;

    protected $table = 'company_statistics';

    protected $fillable = [
        'company_id',
        'total_clients',
        'total_products',
        'total_branches',
        'total_users',
        'total_due_invoices_amount',
        'total_due_invoices_count'
    ];
}
