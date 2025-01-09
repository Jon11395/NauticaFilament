<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Livewire\ListContractExpenses;



Route::get('/', function () {
    return redirect('/admin');
});


//Route::get('contractexpenses', ListContractExpenses::class);
