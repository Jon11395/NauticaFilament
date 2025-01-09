<?php

namespace App\Filament\Resources\SpreadsheetResource\Pages;

use App\Filament\Resources\SpreadsheetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateSpreadsheet extends CreateRecord
{
    use NestedPage;

    protected static string $resource = SpreadsheetResource::class;
}
