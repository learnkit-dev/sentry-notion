<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Notion
{
    public static function boot()
    {
        Http::macro('notion', function () {
            return Http::baseUrl('https://api.notion.com/v1/')
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('NOTION_API_SECRET'),
                    'Content-Type' => 'application/json',
                    'Notion-Version' => '2022-06-28',
                ]);
        });
    }

    public static function getPropertiesForDatabase(string $databaseId): Collection
    {
        $response = Http::notion()->get("databases/{$databaseId}");

        if ($response->status() !== 200) {
            return collect([]);
        }

        $properties = $response->json('properties');

        return collect($properties);
    }

    public static function getSentryBookmarkBlock(string $webUrl): array
    {
        return [
            'type' => 'bookmark',
            'bookmark' => [
                'caption' => [
                    [
                        'type' => 'text',
                        'text' => [
                            'content' => 'Link to Sentry issue',
                        ],
                    ]
                ],
                'url' => $webUrl,
            ],
        ];
    }

    public static function getEmptyBlock(): array
    {
        return [
            'type' => 'paragraph',
            'paragraph' => [
                'rich_text' => [],
            ],
        ];
    }
}
