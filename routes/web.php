<?php

use App\Http\Controllers\Webhook\CreateNotionPageForSentryIssueController;
use App\Http\Controllers\Webhook\LinkNotionPageForSentryIssueController;
use App\Http\Controllers\Webhook\SentryController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return 'bridge is working as expected';
});

Route::group([
    'prefix' => "sentry/{notionDatabaseId?}",
], function () {
    Route::get('issues', function ($notionDatabaseId) {
        $term = request()->input('query');

        $data = [];

        if (filled($term)) {
            $response = Http::notion()->post("databases/{$notionDatabaseId}/query", [
                'filter' => [
                    'or' => [
                        [
                            'property' => 'Task name',
                            'title' => [
                                'contains' => $term,
                            ],
                        ],
                        [
                            'property' => 'Task ID',
                            'unique_id' => [
                                'equals' => extractNumbers($term),
                            ],
                        ]
                    ],
                ],
            ]);

            $data = $response->json('results');

            $data = collect($data)
                ->map(function ($item) {
                    $properties = collect($item['properties'])->map(function ($value, $key) {
                        return [
                            'name' => $key,
                            ...$value,
                        ];
                    });

                    $titleProp = $properties->firstWhere('type', 'title');

                    $title = $item['properties'][$titleProp['name']]['title'][0]['plain_text'];

                    $idProperty = $item['properties']['Task ID']['unique_id'];

                    $id = $idProperty['prefix'] . '-' . $idProperty['number'];

                    return [
                        'label' => "[{$id}] - {$title}",
                        'value' => $item['id'],
                        'default' => false,
                    ];
                })
                ->toArray();
        }

        return $data;
    });

    Route::get('databases', function () {
        $response = Http::notion()->post('search', [
            'filter' => [
                'value' => 'database',
                'property' => 'object',
            ],
        ]);

        $data = $response->json('results');

        return collect($data)->map(function ($item) {
            $title = $item['title'][0]['plain_text'];
            $description = $item['description'][0]['plain_text'] ?? '';

            $label = $description ? "{$title} ({$description})" : $title;

            return [
                'label' => $label,
                'value' => $item['id'],
                'default' => Cache::get('last_used_notion_database') === $item['id'],
            ];
        })->toArray();
    });

    Route::post('issues/link', LinkNotionPageForSentryIssueController::class);

    Route::post('issues/create', CreateNotionPageForSentryIssueController::class)->name('sentry.issue.create');

    Route::post('webhook', SentryController::class);
});
