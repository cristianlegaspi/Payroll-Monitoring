<?php

namespace App\Filament\Resources\DailyTimeRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DailyTimeRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Employee Name')
                    ->searchable(),
                TextColumn::make('work_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('shift1_time_in')
                    ->time()
                    ->sortable(),
                TextColumn::make('shift1_time_out')
                    ->time()
                    ->sortable(),
                TextColumn::make('shift2_time_in')
                    ->time()
                    ->sortable(),
                TextColumn::make('shift2_time_out')
                    ->time()
                    ->sortable(),
                TextColumn::make('shift3_time_in')
                    ->time()
                    ->sortable(),
                TextColumn::make('shift3_time_out')
                    ->time()
                    ->sortable(),
                TextColumn::make('overtime_hours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('undertime_hours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_hours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remarks')
                    ->searchable(),
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
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
