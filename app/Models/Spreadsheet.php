<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Spreadsheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function payment(): HasMany{
        return $this->hasMany(Payment::class);
    }

    
}
