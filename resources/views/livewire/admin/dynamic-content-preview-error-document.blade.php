<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Dynamic content preview') }}</title>
    </head>
    <body>
        <div style="margin: 1.5rem; border: 1px solid #fecaca; border-radius: 0.5rem; background: #fef2f2; padding: 1rem; color: #b91c1c; font-family: sans-serif; font-size: 0.875rem;">
            {{ $message }}
        </div>
    </body>
</html>
