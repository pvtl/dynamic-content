<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JsonException;
use Pvtl\DynamicContent\Models\DynamicContent;

class DynamicContentJsonService
{
    public function export(DynamicContent $dynamicContent): string
    {
        return json_encode([
            'version' => 1,
            'slug' => $dynamicContent->slug,
            'description' => $dynamicContent->description,
            'sections' => $dynamicContent->sections()
                ->get()
                ->map(fn ($section): array => [
                    'slug' => $section->slug,
                    'content' => $section->content,
                    'order' => $section->order,
                ])
                ->all(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    public function replace(DynamicContent $dynamicContent, string $json): void
    {
        try {
            $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw ValidationException::withMessages([
                'importFile' => __('The import file must contain valid JSON.'),
            ]);
        }

        if (! is_array($payload)) {
            throw ValidationException::withMessages([
                'importFile' => __('The import file must contain a dynamic content object.'),
            ]);
        }

        $validated = Validator::validate($payload, [
            'version' => ['required', 'integer', 'in:1'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_-]+$/'],
            'description' => ['required', 'string'],
            'sections' => ['required', 'array'],
            'sections.*' => ['required', 'array'],
            'sections.*.slug' => ['required', 'string', 'max:255'],
            'sections.*.content' => ['required', 'array'],
            'sections.*.order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($dynamicContent, $validated): void {
            $content = DynamicContent::query()
                ->lockForUpdate()
                ->findOrFail($dynamicContent->id);

            if ($content->slug !== $validated['slug']) {
                throw ValidationException::withMessages([
                    'importFile' => __('The import file belongs to a different dynamic content record.'),
                ]);
            }

            $content->update(['description' => $validated['description']]);
            $content->sections()->delete();

            foreach ($validated['sections'] as $section) {
                $content->sections()->create($section);
            }
        });
    }
}
