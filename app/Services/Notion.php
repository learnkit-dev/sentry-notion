<?php

namespace App\Services;

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
}
