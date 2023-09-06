<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\SentryIssueWebhookMapping;
use Illuminate\Http\Request;

class SentryController extends Controller
{
    public function __invoke(Request $request)
    {
        $action = $request->input('action');
        $installation = $request->input('installation.uuid');
        $projectId = $request->input('data.event.project');

        $title = $request->input('data.event.title');
        $webUrl = $request->input('data.event.web_url');

        // Get the mapping from the DB
        $mapping = SentryIssueWebhookMapping::query()
            ->where('project_id', $projectId)
            ->firstOrFail();

        ray($mapping);
    }
}
