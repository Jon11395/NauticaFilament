<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function contractexpenses(): HasMany
    {
        return $this->hasMany(ContractExpense::class);
    }

    
}
