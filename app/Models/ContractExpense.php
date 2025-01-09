<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher',
        'date',
        'concept',
        'total_solicited',
        'retentions',
        'CCSS',
        'total_deposited',
        'contract_id',
        
    ];

    public function contract(){
        return $this->belongsTo(Contract::class);
    }
}
