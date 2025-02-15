<?php

namespace App\Livewire;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Tables\Actions\EditAction;
use App\Models\ContractExpense;
use Filament\Actions\DeleteAction;
use function Laravel\Prompts\form;
use Illuminate\Contracts\View\View;
use App\Services\ContractExpenseForm;
use App\Services\ContractExpensesForm;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\Summarizers\Sum;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class ListContractExpenses extends Component implements HasForms, HasTable
{

    use InteractsWithTable, InteractsWithForms;
 
    public ContractExpense $contractexpense;

    public $record;

    public function mount($record){
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ContractExpense::where('contract_id', $this->record->id))
            ->heading('Gastos')
            ->description($this->record->name. ' - Monto del contrato: '. number_format($this->record->amount, 2). ' CRC')
            ->columns([
                Tables\Columns\TextColumn::make('voucher')
                    ->label('Comprobante')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('concept')
                    ->label('Concepto')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_solicited')
                    ->label('Solicitado')
                    ->money('CRC')
                    ->summarize(Sum::make()->label('Total')->money('CRC')),
                Tables\Columns\TextColumn::make('retentions')
                    ->label('Retenciones')
                    ->money('CRC')
                    ->summarize(Sum::make()->label('Total')->money('CRC')),
                Tables\Columns\TextColumn::make('CCSS')
                    ->label('CCSS')
                    ->money('CRC')
                    ->summarize(Sum::make()->label('Total')->money('CRC')),
                Tables\Columns\TextColumn::make('total_deposited')
                    ->label('Depositado')
                    ->money('CRC')
                    ->summarize(Sum::make()->label('Total')->money('CRC')),
            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Gasto nuevo')
                    ->model(ContractExpense::class)
                    ->form(ContractExpensesForm::schema($this->record->id))
            ])
            ->actions([
                    Tables\Actions\EditAction::make()
                        ->form(ContractExpensesForm::schema($this->record->id)),
                    Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('Exportar'),
                ]),
            ]);
    }

 
    
    

    public function render()
    {
        return view('livewire.list-contract-expenses');
    }
}
