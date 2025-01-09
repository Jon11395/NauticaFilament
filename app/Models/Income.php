<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'date',
        'bill_amount',
        'IVA',
        'retentions',
        'description',
        'total_deposited',
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
