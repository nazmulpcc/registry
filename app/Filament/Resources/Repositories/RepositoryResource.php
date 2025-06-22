<?php

namespace App\Filament\Resources\Repositories;

use App\Enums\Status;
use App\Filament\Columns\StatusColumn;
use App\Filament\Resources\Repositories\Pages\CreateRepository;
use App\Filament\Resources\Repositories\Pages\EditRepository;
use App\Filament\Resources\Repositories\Pages\ListRepositories;
use App\Filament\Resources\Repositories\RelationManagers\PermissionsRelationManager;
use App\Models\Repository;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Square3Stack3d;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->readOnlyOn('edit')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set){
                        $set('fqn', $state);
                    })
                    ->required()
                    ->helperText(__('The name of the repository, should be lowercase and alphanumeric.')),
                TextInput::make('fqn')
                    ->readOnly()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label(__('FQN'))
                    ->prefix(function (?Repository $record): string {
                        return $record?->exists
                            ? Str::before($record->fqn, '/') . '/'
                            : filament()->auth()->user()->username . '/';
                    })
                    ->formatStateUsing(function (?string $state): ?string {
                        return Str::afterLast($state, '/');
                    })
                    ->dehydrateStateUsing(function (TextInput $component, string $state){
                        return $component->getPrefixLabel() . $state;
                    }),
                Select::make('status')
                    ->required()
                    ->enum(Status::class)
                    ->default(Status::Active),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('creator.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fqn')
                    ->searchable(),
                StatusColumn::make(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRepositories::route('/'),
            'edit' => EditRepository::route('/{record}/edit'),
        ];
    }
}
