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
            callback: function (string $sectionKey, array $sectionData, array $field): void {
                $value = $sectionData['fields'][$field['slug']] ?? null;

                if (! ($value instanceof TemporaryUploadedFile)) {
                    return;
                }

                $disk = config('dynamic_content.disk');
                $existingPath = $this->existingFilePath($sectionKey, $field['slug']);
                $path = $value->store('sections', $disk);
                $this->sections[$sectionKey]['fields'][$field['slug']] = $path;

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
            callback: function (string $sectionKey, array $sectionData, array $field) use (&$rules, &$attributes): void {
                $rules["sections.{$sectionKey}.fields.{$field['slug']}"] = $field['validation'];
                $attributes["sections.{$sectionKey}.fields.{$field['slug']}"] = $field['name'];
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

            foreach ($config['fields'] as $field) {
                if ($onlyTypes && ! in_array($field['type'], $onlyTypes, strict: true)) {
                    continue;
                }

                if ($skipTypes && in_array($field['type'], $skipTypes, strict: true)) {
                    continue;
                }

                $callback($sectionKey, $sectionData, $field);
            }
        }
    }

    protected function existingFilePath(string $sectionKey, string $fieldSlug): ?string
    {
        $sectionId = $this->sections[$sectionKey]['id'] ?? null;

        return $sectionId
            ? DynamicContentSection::find($sectionId)?->content[$fieldSlug] ?? null
            : null;
    }
}
