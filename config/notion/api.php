<?php

return [
    'url' => "https://api.notion.com/v1",
    'backlogDatabaseUrl' => env('NOTION_BACKLOG_DATABASE_URL'),
    'parentDatabaseUrl' => env('NOTION_PARENT_DATABASE_URL'),
    'token' => env('NOTION_API_TOKEN')
];
