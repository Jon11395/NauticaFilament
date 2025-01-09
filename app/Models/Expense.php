<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher',
        'date',
        'concept',
        'amount',
        'type',
        'provider_id',
        'expense_type_id',
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }
    public function provider(){
        return $this->belongsTo(Provider::class);
    }

    public function ExpenseType(){
        return $this->belongsTo(ExpenseType::class);
    }
}
