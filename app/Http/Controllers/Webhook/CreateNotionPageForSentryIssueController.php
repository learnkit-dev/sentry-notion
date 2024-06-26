<?php

namespace App\Http\Controllers\Webhook;

use App\Exceptions\NotionPageException;
use App\Http\Controllers\Concerns\InteractsWithNotionBlocks;
use App\Http\Controllers\Controller;
use App\Services\Notion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CreateNotionPageForSentryIssueController extends Controller
{
    use InteractsWithNotionBlocks;

    public function __invoke($notionDatabaseId)
    {
        [$notionPageId, $identifier, $url] = $this->createNotionPage(
            databaseId: $notionDatabaseId,
            title: request()->input('fields.title'),
        );

        $this->addSentryWebUrlAsBookmarkToNotionPage(
            notionPageId: $notionPageId,
            sentryWebUrl: request()->input('webUrl'),
        );

        return [
            'webUrl' => $url,
            'project' => request()->input('project.slug'),
            'identifier' => $identifier,
        ];
    }

    private function createNotionPage(string $databaseId, string $title): array
    {
        Cache::put('last_used_notion_database', $databaseId);

        $props = Notion::getPropertiesForDatabase($databaseId);

        $conditionalProperties = [];

        $titleProp = $props->firstWhere('type', 'title');

        $tagsProp = $props
            ->where('type', 'multi_select')
            ->where('name', 'Tags')
            ->first();

        if (filled($tagsProp)) {
            $conditionalProperties[$tagsProp['name']]['multi_select'] = [
                [
                    'name' => 'Sentry issue',
                ]
            ];
        }

        $response = Http::notion()->post('pages', [
            'parent' => [
                'database_id' => $databaseId,
            ],
            'properties' => [
                $titleProp['name'] => [
                    'title' => [
                        [
                            'text' => [
                                'content' => $title,
                            ],
                        ]
                    ],
                ],
                ...$conditionalProperties,
            ],
            'icon' => [
                'type' => 'external',
                'external' => [
                    'url' => asset('icons/sentry-dark.png'),
                ],
            ],
        ]);

        if ($response->status() !== 200) {
            throw new NotionPageException('Could not create page for database: ' . $databaseId);
        }

        $data = $response->json();

        $properties = collect($data['properties']);

        $idProp = $properties->firstWhere('type', 'unique_id');

        $id = implode('-', [
            $idProp['unique_id']['prefix'],
            $idProp['unique_id']['number'],
        ]);

        return [
            $data['id'],
            $id,
            $data['url'],
        ];
    }
}
