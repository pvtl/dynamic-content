# pvtl/dynamic-content

Dynamic content management with configurable sections for Laravel Livewire and Flux UI applications. Define section types in a config file, manage content through an admin UI, and render sections on the frontend using Blade components.

## Requirements

- PHP ^8.4
- Laravel ^13.0
- Livewire ^4.0
- Flux UI ^2.0 (Pro)

## Installation

```bash
composer require pvtl/dynamic-content
```

Publish resources and run migrations:

```bash
php artisan pvtl-dynamic-content:publish
php artisan migrate
```

## What Gets Published

`pvtl-dynamic-content:publish` installs:

| Resource | Destination |
|---|---|
| Migrations | `database/migrations/` |
| Package config | `config/dynamic_content.php` |
| Routes | `routes/dynamic_content.php` |
| Sections config stub | `config/sections.php` |

To also publish the section field Blade components for customisation:

```bash
php artisan pvtl-dynamic-content:publish-sections
```

This copies the field input components to `resources/views/components/sections/`. Once published, the package uses your copies instead of its own defaults.

## Configuration

### `config/dynamic_content.php`

```php
return [
    // Filesystem disk for section image/file uploads.
    'disk' => env('DYNAMIC_CONTENT_DISK', 'public'),

    // Blade component directory for frontend section renderers.
    // 'dynamic' resolves a section component named 'homepage-hero'
    // to <x-dynamic.homepage-hero>.
    'component_directory' => env('DYNAMIC_CONTENT_COMPONENT_DIR', 'dynamic'),
];
```

### Routes

The published `routes/dynamic_content.php` contains the admin CRUD routes. Customise them as needed — add middleware, change URIs, or wrap them in a route group to suit your application's auth setup.

## Defining Sections

Edit the published `config/sections.php` to define the section types available in the admin panel. The `homepage-hero` example is included and ready to use:

```php
use Pvtl\DynamicContent\Enums\SectionFieldType;

return [
    [
        'slug'        => 'homepage-hero',
        'component'   => 'homepage-hero',
        'description' => 'Hero banner displayed at the top of the homepage.',
        'fields'      => [
            [
                'name'       => 'Heading',
                'slug'       => 'heading',
                'description'=> 'Main headline text.',
                'type'       => SectionFieldType::Text,
                'class'      => 'w-1/2',
                'default'    => '',
                'validation' => ['required', 'string', 'max:255'],
                'options'    => [],
            ],
            // ...more fields
        ],
    ],
];
```

Refer to the schema comment at the top of `config/sections.php` for the full list of available field types and options.

## Creating Frontend Section Components

Each section type needs a Blade component that renders it on the frontend. Components live in `resources/views/components/{component_directory}/` (default: `resources/views/components/dynamic/`).

### How it works

When the `DynamicContentRenderer` outputs a section, it calls:

```blade
<x-dynamic-component
    :component="config('dynamic_content.component_directory') . '.' . $sectionConfig['component']"
    :attrs="$section->content" />
```

Your component receives all stored field values as the `$attrs` array. **Components must never query the database** — all data is passed via `$attrs`.

### Example: `homepage-hero`

Create `resources/views/components/dynamic/homepage-hero.blade.php`:

```blade
@props(['attrs' => []])

@php
    $heading    = $attrs['heading'] ?? null;
    $body       = $attrs['body'] ?? null;
    $layout     = $attrs['layout'] ?? 'left';
    $bgImage    = $attrs['background_image'] ?? null;

    $alignClass = match ($layout) {
        'center' => 'text-center items-center',
        'right'  => 'text-right items-end',
        default  => 'text-left items-start',
    };

    $imageUrl = $bgImage
        ? \Illuminate\Support\Facades\Storage::disk(config('dynamic_content.disk'))->url($bgImage)
        : null;
@endphp

<section class="relative overflow-hidden bg-zinc-900">
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-50" />
    @endif

    <div class="relative mx-auto max-w-7xl px-6 py-24 lg:px-8 lg:py-32">
        <div class="flex flex-col {{ $alignClass }} gap-6">
            @if ($heading)
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">
                    {{ $heading }}
                </h1>
            @endif

            @if ($body)
                <p class="max-w-2xl text-lg text-white/80">{{ $body }}</p>
            @endif
        </div>
    </div>
</section>
```

### Key rules for section components

- Always declare `@props(['attrs' => []])`.
- Access fields via `$attrs['field_slug']` — use `?? null` for optional fields.

## Rendering Dynamic Content

Use `DynamicContentRenderer` anywhere in your Blade views:

```blade
<livewire:dynamic-content-renderer slug="homepage" />
```

The component loads the `DynamicContent` record by slug (creating it if it does not exist), loops through its sections ordered by the `order` column, and renders each one using its configured Blade component.
