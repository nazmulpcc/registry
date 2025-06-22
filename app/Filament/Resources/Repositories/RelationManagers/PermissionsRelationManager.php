<?php

namespace App\Filament\Resources\Repositories\RelationManagers;

use App\Models\RepositoryPermission;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property \App\Models\Repository $ownerRecord
 */
class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    protected static ?string $title = 'User Permissions';

    public function form(Schema $schema): Schema
    {
        $getUsers = function (?string $search = null) {
            return \App\Models\User::query()
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                })
                ->limit(10)
                ->pluck('name', 'id');
        };

        return $schema
            ->columns(1)
            ->schema([
                Select::make('user_id')
                    ->label(__('User'))
                    ->searchable()
                    ->options($getUsers)
                    ->getSearchResultsUsing($getUsers)
                    ->required(),
                Section::make(__('Permissions'))
                    ->columns(6)
                    ->schema([
                        Toggle::make('permissions.read')
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, bool $state) {
                                if (!$state) {
                                    $set('permissions.write', false);
                                    $set('permissions.admin', false);
                                }
                            })
                            ->default(true),
                        Toggle::make('permissions.write')
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, bool $state) {
                                if ($state) {
                                    $set('permissions.read', true);
                                } else {
                                    $set('permissions.admin', false);
                                }
                            })
                            ->default(false),
                        Toggle::make('permissions.admin')
                            ->live(true)
                            ->afterStateUpdated(function (Set $set, bool $state) {
                                if ($state) {
                                    $set('permissions.write', true);
                                    $set('permissions.read', true);
                                }
                            })
                            ->default(false),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {

        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('formated_permissions')
                    ->label(__('Permissions')),
            ])
            ->defaultSort('created_at', 'desc')
            ->query(function (){
                return $this->ownerRecord->permissions()->with(['user', 'repository']);
            })
            ->allowDuplicates()
            ->headerActions([
                CreateAction::make()
                    ->label(__('Add User'))
                    ->icon(Heroicon::PlusCircle),
            ])
            ->recordActions([
                EditAction::make('edit')
            ]);
    }
}
