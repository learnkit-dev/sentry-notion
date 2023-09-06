<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentryIssueWebhookMapping extends Model
{
    protected $casts = [
        'field_mapping' => 'json',
        'actions' => 'array',
        'is_active' => 'bool',
    ];

    protected $guarded = [];
}
