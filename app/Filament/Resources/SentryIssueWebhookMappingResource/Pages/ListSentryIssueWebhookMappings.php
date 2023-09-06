<?php

namespace App\Filament\Resources\SentryIssueWebhookMappingResource\Pages;

use App\Filament\Resources\SentryIssueWebhookMappingResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSentryIssueWebhookMappings extends ListRecords
{
    protected static string $resource = SentryIssueWebhookMappingResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
