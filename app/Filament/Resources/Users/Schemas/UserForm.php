<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Status;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('status')
                    ->required()
                    ->enum(Status::class)
                    ->default(Status::Active),
            ]);
    }
}
