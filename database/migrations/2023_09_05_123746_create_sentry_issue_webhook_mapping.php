<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sentry_issue_webhook_mappings', function (Blueprint $table) {
            $table->id();

            $table->string('installation')->nullable();
            $table->json('actions')->nullable();
            $table->integer('project_id')->nullable();
            $table->string('notion_database_id')->nullable();
            $table->json('field_mapping')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }
};
