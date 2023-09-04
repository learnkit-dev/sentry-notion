<?php

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
                return [
                    'label' => $item['properties']['Name']['title'][0]['plain_text'],
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
        return [
            'label' => $item['title'][0]['plain_text'],
            'value' => $item['id'],
            'default' => false,
        ];
    })->toArray();
});

Route::post('/sentry/issues/link', function () {
    $pageId = request()->input('fields.issue_id');

    $response = Http::notion()->patch("pages/{$pageId}", [
        'properties' => [
            'Sentry issue' => [
                'url' => request()->input('webUrl'),
            ],
        ],
    ]);

    $data = $response->json();

    return [
        'webUrl' => $data['url'],
        'project' => request()->input('project.slug'),
        'identifier' => $data['properties']['ID']['unique_id']['prefix'] . '-' . $data['properties']['ID']['unique_id']['number'],
    ];
});

Route::post('/sentry/issues/create', function () {
    $response = Http::notion()->post('pages', [
        'parent' => [
            'database_id' => request()->input('fields.database'),
        ],
        'properties' => [
            'Name' => [
                'title' => [
                    [
                        'text' => [
                            'content' => request()->input('fields.title'),
                        ],
                    ]
                ],
            ],
            'Sentry issue' => [
                'url' => request()->input('webUrl'),
            ],
        ],
    ]);

    $data = $response->json();

    return [
        'webUrl' => $data['url'],
        'project' => request()->input('project.slug'),
        'identifier' => $data['properties']['ID']['unique_id']['prefix'] . '-' . $data['properties']['ID']['unique_id']['number'],
    ];
});
