<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">

        <title><?= getenv('APP_NAME') ?> &mdash; Home</title>

        <style>
            body {
                font-family: 'Arial', sans-serif;
            }
            .centered {
                height: 100%;
                text-align: center;
                padding: 20% 0 0;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="centered">
            <p>Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})</p>
        </div>
    </body>
</html>
