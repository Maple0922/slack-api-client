<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Slack API Client</title>
        @vite(['resources/css/app.scss', 'resources/js/app.ts'])
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
