<?php

use App\Http\Controllers\Webhook\CreateNotionPageForSentryIssueController;
use App\Http\Controllers\Webhook\LinkNotionPageForSentryIssueController;
use App\Http\Controllers\Webhook\SentryController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/sentry/issues', function () {
    $term = request()->input('query');

    $data = [];

    if (filled($term)) {
        $response = Http::notion()->post('search', [
            'query' => $term,
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

                return [
                    'label' => $item['properties'][$titleProp['name']]['title'][0]['plain_text'],
                    'value' => $item['id'],
                    'default' => false,
                ];
            })
            ->toArray();
    }

    return $data;
});

Route::get('/sentry/databases', function () {
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
            'default' => false,
        ];
    })->toArray();
});

Route::post('/sentry/issues/link', LinkNotionPageForSentryIssueController::class);

Route::post('/sentry/issues/create', CreateNotionPageForSentryIssueController::class)->name('sentry.issue.create');

Route::post('/sentry/webhook', SentryController::class);
