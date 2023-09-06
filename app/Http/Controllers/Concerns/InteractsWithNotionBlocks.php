<?php

namespace App\Http\Controllers\Concerns;

use App\Exceptions\NotionBlockException;
use App\Services\Notion;
use Illuminate\Support\Facades\Http;

trait InteractsWithNotionBlocks
{
    private function addSentryWebUrlAsBookmarkToNotionPage(string $notionPageId, string $sentryWebUrl)
    {
        $response = Http::notion()->patch("blocks/{$notionPageId}/children", [
            'children' => [
                Notion::getSentryBookmarkBlock(
                    webUrl: $sentryWebUrl,
                ),
            ],
        ]);

        if ($response->status() !== 200) {
            throw new NotionBlockException('Could not create Sentry bookmark block for page: ' . $notionPageId);
        }
    }
}
