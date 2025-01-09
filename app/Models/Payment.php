<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary',
        'description',
        'employee_id',
        'spreadsheet_id'
    ];

    public function spreadsheet(){
        return $this->belongsTo(Spreadsheet::class);
    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
