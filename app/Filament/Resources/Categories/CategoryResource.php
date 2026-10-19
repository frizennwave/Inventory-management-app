<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordTitleAttribute = 'Category';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama kategori')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Category')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama kategori')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotificationTitle('Kategori berhasil diubah!'),
                DeleteAction::make()
                    ->successNotificationTitle('Kategori berhasil dihapus')
                    ->before(function (DeleteAction $action, Category $record) {
                        if ($record->items()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('Kategori gagal dihapus!')
                                ->body('Kategori masih memiliki item aktif. Pindahkan item terlebih dahulu.')
                                ->persistent()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotificationTitle('Kategori yang anda pilih berhasil dihapus')
                        ->before(function (DeleteBulkAction $action, Collection $records) {
                            $hasItems = $records->contains(fn (Category $category) => $category->items()->exists());

                            if ($hasItems) {
                                Notification::make()
                                    ->danger()
                                    ->title('Kategori Gagal Dihapus!')
                                    ->body('Satu atau beberapa kategori yang anda pilih masih memiliki item aktif. Operasi dibatalkan demi keamanan data.')
                                    ->persistent()
                                    ->send();

                                $action->halt();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
