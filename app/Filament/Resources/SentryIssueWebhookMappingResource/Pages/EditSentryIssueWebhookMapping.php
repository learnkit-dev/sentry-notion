<?php

namespace App\Filament\Resources\SentryIssueWebhookMappingResource\Pages;

use App\Filament\Resources\SentryIssueWebhookMappingResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSentryIssueWebhookMapping extends EditRecord
{
    protected static string $resource = SentryIssueWebhookMappingResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
