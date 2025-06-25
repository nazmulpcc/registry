<?php

namespace App\Filament\Resources\Tokens\Pages;

use App\Filament\Resources\Tokens\TokenResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Wizard\Step;

class ManageTokens extends ManageRecords
{
    protected static string $resource = TokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create-token')
                ->label(__('Create Token'))
                ->icon('heroicon-o-key')
                ->color('primary')
                ->steps([
                    Step::make('Name & Expiration')
                        ->schema(TokenResource::form($this->makeSchema())->getComponents()),
                    Step::make('Save Token')
                        ->schema([])
                ]),
        ];
    }
}
