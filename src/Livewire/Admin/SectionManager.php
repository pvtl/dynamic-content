<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Pvtl\DynamicContent\Enums\SectionFieldType;
use Pvtl\DynamicContent\Models\DynamicContent as DynamicContentModel;
use Pvtl\DynamicContent\Models\DynamicContentSection;

class SectionManager extends Component
{
    use WithFileUploads;

    private const array UPLOAD_FIELD_TYPES = [SectionFieldType::ImageUpload, SectionFieldType::DownloadableFile];

    public array $sections = [];

    public bool $addSectionModalOpen = false;

    public function mount(array $initialSections = []): void
    {
        $this->sections = collect($initialSections)->keyBy('key')->toArray();
    }

    #[Computed]
    public function sectionConfigs(): array
    {
        return collect(config('sections', []))->keyBy('slug')->toArray();
    }

    public function addSection(string $slug): void
    {
        $config = $this->sectionConfigs[$slug] ?? null;

        if (! $config) {
            return;
        }

        $key = uniqid('section_');

        $this->sections[$key] = [
            'id' => null,
            'key' => $key,
            'slug' => $slug,
            'fields' => collect($config['fields'])
                ->mapWithKeys(fn ($field) => [$field['slug'] => $field['default'] ?? null])
                ->all(),
        ];

        $this->addSectionModalOpen = false;
    }

    public function removeSection(string $key): void
    {
        unset($this->sections[$key]);
    }

    public function addRepeaterRow(string $path): void
    {
        $fieldConfig = $this->resolveFieldConfig($path);

        if (! $fieldConfig) {
            return;
        }

        $rows = (array) data_get($this, $path, []);
        $rows[$this->generateRowKey()] = $this->defaultRowValues($fieldConfig['fields'] ?? []);

        data_set($this, $path, $rows);
    }

    public function removeRepeaterRow(string $path, string $rowKey): void
    {
        $rows = (array) data_get($this, $path, []);
        unset($rows[$rowKey]);

        data_set($this, $path, $rows);
    }

    public function sortSections(string $id, int $position): void
    {
        $keys = array_keys($this->sections);
        $currentIndex = array_search($id, $keys, strict: true);

        if ($currentIndex === false) {
            return;
        }

        array_splice($keys, $currentIndex, 1);
        array_splice($keys, $position, 0, [$id]);

        $reordered = [];
        foreach ($keys as $key) {
            $reordered[$key] = $this->sections[$key];
        }

        $this->sections = $reordered;
    }

    #[On('dc-saved')]
    public function saveSections(int $dynamicContentId): void
    {
        $this->validateSectionFields();
        $this->processSectionUploads();

        $dynamicContent = DynamicContentModel::query()->findOrFail($dynamicContentId);

        $keepIds = collect($this->sections)->pluck('id')->filter()->all();

        $dynamicContent->sections()->whereNotIn('id', $keepIds)->delete();

        foreach (array_values($this->sections) as $order => $sectionData) {
            if ($sectionData['id'] !== null) {
                $dynamicContent->sections()
                    ->where('id', $sectionData['id'])
                    ->update(['content' => json_encode($sectionData['fields']), 'order' => $order]);
            } else {
                $dynamicContent->sections()->create([
                    'slug' => $sectionData['slug'],
                    'content' => $sectionData['fields'],
                    'order' => $order,
                ]);
            }
        }

        $this->dispatch('dc-sections-saved');
    }

    public function render(): View
    {
        return view('dynamic-content::livewire.admin.section-manager');
    }

    protected function processSectionUploads(): void
    {
        $this->eachSectionField(
            callback: function (string $path, array $field): void {
                $value = data_get($this, $path);

                if (! ($value instanceof TemporaryUploadedFile)) {
                    return;
                }

                $disk = config('dynamic_content.disk');
                $existingPath = $this->existingFilePath($path);
                $storedPath = $value->store('sections', $disk);

                data_set($this, $path, $storedPath);

                if ($existingPath) {
                    Storage::disk($disk)->delete($existingPath);
                }
            },
            onlyTypes: self::UPLOAD_FIELD_TYPES,
        );
    }

    protected function validateSectionFields(): void
    {
        $rules = [];
        $attributes = [];

        $this->eachSectionField(
            callback: function (string $path, array $field) use (&$rules, &$attributes): void {
                $rules[$path] = $field['validation'];
                $attributes[$path] = $field['name'];
            },
            skipTypes: self::UPLOAD_FIELD_TYPES,
        );

        if (! empty($rules)) {
            $this->validate($rules, [], $attributes);
        }
    }

    protected function eachSectionField(callable $callback, array $onlyTypes = [], array $skipTypes = []): void
    {
        foreach ($this->sections as $sectionKey => $sectionData) {
            $config = $this->sectionConfigs[$sectionData['slug']] ?? null;

            if (! $config) {
                continue;
            }

            $this->walkFields($config['fields'], "sections.{$sectionKey}.fields", $callback, $onlyTypes, $skipTypes);
        }
    }

    protected function walkFields(array $fields, string $path, callable $callback, array $onlyTypes, array $skipTypes): void
    {
        foreach ($fields as $field) {
            $fieldPath = "{$path}.{$field['slug']}";

            $matchesOnly = ! $onlyTypes || in_array($field['type'], $onlyTypes, strict: true);
            $matchesSkip = $skipTypes && in_array($field['type'], $skipTypes, strict: true);

            if ($matchesOnly && ! $matchesSkip) {
                $callback($fieldPath, $field);
            }

            if ($field['type'] === SectionFieldType::Repeater) {
                $rows = (array) data_get($this, $fieldPath, []);

                foreach (array_keys($rows) as $rowKey) {
                    $this->walkFields($field['fields'] ?? [], "{$fieldPath}.{$rowKey}", $callback, $onlyTypes, $skipTypes);
                }
            }
        }
    }

    protected function resolveFieldConfig(string $path): ?array
    {
        $segments = explode('.', $path);
        $sectionKey = $segments[1] ?? null;
        $sectionSlug = $sectionKey ? ($this->sections[$sectionKey]['slug'] ?? null) : null;
        $config = $sectionSlug ? ($this->sectionConfigs[$sectionSlug] ?? null) : null;

        if (! $config) {
            return null;
        }

        $fields = $config['fields'];
        $current = null;
        $expectingFieldSlug = true;

        foreach (array_slice($segments, 3) as $segment) {
            if (! $expectingFieldSlug) {
                // This segment is a repeater row key, not a field slug — skip over it.
                $expectingFieldSlug = true;

                continue;
            }

            $current = collect($fields)->first(fn (array $field): bool => $field['slug'] === $segment);

            if (! $current) {
                return null;
            }

            if ($current['type'] === SectionFieldType::Repeater) {
                $fields = $current['fields'] ?? [];
                $expectingFieldSlug = false;
            }
        }

        return $current;
    }

    protected function defaultRowValues(array $fields): array
    {
        return collect($fields)
            ->mapWithKeys(fn (array $field) => [
                $field['slug'] => $field['type'] === SectionFieldType::Repeater ? [] : ($field['default'] ?? null),
            ])
            ->all();
    }

    protected function generateRowKey(): string
    {
        return uniqid('row_');
    }

    protected function existingFilePath(string $path): ?string
    {
        [$sectionKey, $relativePath] = $this->splitFieldPath($path);

        $sectionId = $this->sections[$sectionKey]['id'] ?? null;

        if (! $sectionId) {
            return null;
        }

        $content = DynamicContentSection::query()->find($sectionId)?->content;

        return $content ? data_get($content, $relativePath) : null;
    }

    /** @return array{0: string, 1: string} */
    protected function splitFieldPath(string $path): array
    {
        $segments = explode('.', $path);

        return [$segments[1], implode('.', array_slice($segments, 3))];
    }
}
