<?php

namespace App\Http\Controllers\Webhook;

use App\Exceptions\NotionPageException;
use App\Http\Controllers\Concerns\InteractsWithNotionBlocks;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class LinkNotionPageForSentryIssueController extends Controller
{
    use InteractsWithNotionBlocks;

    public function __invoke()
    {
        $pageId = request()->input('fields.issue_id');

        $this->addSentryWebUrlAsBookmarkToNotionPage(
            notionPageId: $pageId,
            sentryWebUrl: request()->input('webUrl'),
        );

        [$identifier, $webUrl] = $this->getNotionPage(notionPageId: $pageId);

        return [
            'webUrl' => $webUrl,
            'project' => request()->input('project.slug'),
            'identifier' => $identifier,
        ];
    }

    private function getNotionPage(string $notionPageId): array
    {
        $response = Http::notion()->get("pages/$notionPageId");

        if ($response->status() !== 200) {
            throw new NotionPageException('Could not find page: ' . $notionPageId);
        }

        $data = $response->json();

        $properties = collect($data['properties']);

        $idProp = $properties->firstWhere('type', 'unique_id');

        $id = implode('-', [
            $idProp['unique_id']['prefix'],
            $idProp['unique_id']['number'],
        ]);

        return [
            $id,
            $data['url'],
        ];
    }
}
