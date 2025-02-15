<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Expense;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Resources\Components\Tab;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Altwaireb\World\Models\State;
use Altwaireb\World\Models\City;
use Illuminate\Support\Collection;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class ManageProjectExpenses extends ManageRelatedRecords
{
    use NestedPage;

    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'expenses';

    protected static ?string $navigationIcon = 'heroicon-c-arrow-trending-down';

    public static function getNavigationLabel(): string
    {
        return 'Gastos';
    }

    public function getTitle(): string | Htmlable
    {
        return __('Gastos - '. $this->record->name);
    }

    public function getTabs(): array
    {
        $tabs = [];

        $tabs[] = Tab::make('Todas') 
            ->badge(Expense::where('project_id', $this->record->id)->count())
            ->icon('heroicon-s-arrow-right-circle')
            ->badgeColor('info');
    
        $tabs[] = Tab::make('Pagas') 
            ->badge(Expense::where('project_id', $this->record->id)->where('type', 'paid')->count())
            ->icon('heroicon-s-arrow-trending-up') 
            ->badgeColor('success')
            ->modifyQueryUsing(function ($query) {
                return $query->where('type', 'paid');
            });
        $tabs[] = Tab::make('Por pagar') 
            ->badge(Expense::where('project_id', $this->record->id)->where('type', 'unpaid')->count())
            ->icon('heroicon-s-arrow-trending-down')
            ->badgeColor('warning')
            ->modifyQueryUsing(function ($query) {
                return $query->where('type', 'unpaid');
            });
    

    
        return $tabs;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('voucher')
                    ->label('Comprobante')
                    ->required()
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->label('Fecha')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('concept')
                    ->label('Concepto')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->prefix('₡')
                    ->required()
                    ->numeric()
                    ->currencyMask(thousandSeparator: '.',decimalSeparator: ',',precision: 2),
                Forms\Components\Select::make('type')
                    ->options([
                        'paid' => 'Pago',
                        'unpaid' => 'Por Pagar',
                    ])
                    ->required(),
                Forms\Components\Select::make('provider_id')
                    ->label('Proveedor')
                    ->relationship(name:'provider', titleAttribute:'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        PhoneInput::make('phone')
                            ->initialCountry('cr'),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Select::make('country_id')
                            ->label('País')
                            ->relationship(name:'country', titleAttribute:'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            }) 
                            ->required(),
                        Forms\Components\Select::make('state_id')
                            ->options(fn (Get $get): Collection => State::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id')
                            )
                            ->label('Estado o Provincia')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('city_id', null);
                            }) 
                            ->required(),
                        Forms\Components\Select::make('city_id')
                            ->options(fn (Get $get): Collection => City::query()
                                ->where('state_id', $get('state_id'))
                                ->pluck('name', 'id')
                            )
                            ->label('Ciudad')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
                Forms\Components\Select::make('expense_type_id')
                    ->label('Tipo de gasto')
                    ->relationship(name:'ExpenseType', titleAttribute:'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),
                    ]),
                

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Gastos')
            ->description('Lista de gastos')
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
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('CRC')
                    ->summarize(Sum::make()->label('Total')->money('CRC')),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Paga',
                        'unpaid' => 'Por pagar',
                    }),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Proveedor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ExpenseType.name')
                    ->label('Tipo')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('ExpenseType')
                    ->label('Tipo')
                    ->relationship('ExpenseType', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Desde'),
                        DatePicker::make('created_until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Crear gasto'),
                //Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                //Tables\Actions\DissociateAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DissociateBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('Exportar'),
                ]),
            ]);
    }
    
}
