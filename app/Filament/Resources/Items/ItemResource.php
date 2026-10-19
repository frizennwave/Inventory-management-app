<?php

namespace App\Filament\Resources\Items;

use App\Filament\Resources\Items\Pages\ManageItems;
use App\Models\Item;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $recordTitleAttribute = 'Item';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama item')
                    ->required(),
                Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('price')
                    ->label('Harga')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->rules(['gt:0'])
                    ->validationMessages([
                        'gt' => 'Harga harus lebih dari nol.',
                        'numeric' => 'Harga harus berupa angka.',
                    ])
                    ->prefix('IDR'),
                TextInput::make('stock')
                    ->label('Stok')
                    ->required()
                    ->numeric()
                    ->rules(['integer', 'min:0'])
                    ->validationMessages([
                        'integer' => 'Stok harus berupa angka bulat.',
                        'min' => 'Stok minimal adalah :min',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Item')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama item')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotificationTitle('Item berhasil diubah!'),
                DeleteAction::make()
                    ->successNotificationTitle('Item berhasil dihapus!'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotificationTitle('Item yang anda pilih berhasil dihapus!'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageItems::route('/'),
        ];
    }
}
