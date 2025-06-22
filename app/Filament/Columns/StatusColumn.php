<?php

namespace App\Filament\Columns;

use App\Enums\Status;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Model;

class StatusColumn extends IconColumn
{
    protected string $column;

    public static function make(?string $name = null): static
    {
        $instance = parent::make($name ?? 'status');
        $instance->column($name ?? 'status');

        return $instance;
    }

    public function column(string $name): static
    {
        $this->column = $name;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->alignCenter()
            ->getStateUsing(fn(Model $record) => $record->{$this->column}->isActive())
            ->action(Action::make('toggle-status')
                ->color(Color::Red)
                ->icon(Heroicon::AdjustmentsHorizontal)
                ->requiresConfirmation()
                ->successNotificationTitle(__('Status updated.'))
                ->action(fn(Model $record) => $record->update([
                    'status' => $record->{$this->column}->isActive()
                        ? Status::InActive
                        : Status::Active,
                ])))
            ->boolean();
    }
}
