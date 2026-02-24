<?php

namespace App\Filament\Resources\DailyTimeRecords\Pages;

use App\Filament\Resources\DailyTimeRecords\DailyTimeRecordResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;


class ViewDailyTimeRecord extends ViewRecord
{
    protected static string $resource = DailyTimeRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }


       protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

      protected function getCreatedNotificationBody(): ?string
    {
        return 'The DTR record has been created successfully.';
    }

     protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->success()
        ->title('DTR Record Created')
        ->body($this->getCreatedNotificationBody());
    }


}
