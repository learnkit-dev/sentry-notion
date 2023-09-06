<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SentryIssueWebhookMappingResource\Pages;
use App\Models\SentryIssueWebhookMapping;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class SentryIssueWebhookMappingResource extends Resource
{
    protected static ?string $model = SentryIssueWebhookMapping::class;

    protected static ?string $slug = 'sentry-issue-webhook-mappings';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ''
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSentryIssueWebhookMappings::route('/'),
            'create' => Pages\CreateSentryIssueWebhookMapping::route('/create'),
            'edit' => Pages\EditSentryIssueWebhookMapping::route('/{record}/edit'),
        ];
    }
}
