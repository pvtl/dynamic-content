<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Enums;

enum SectionFieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case RichEditor = 'rich_editor';
    case Number = 'number';
    case Bool = 'bool';
    case Select = 'select';
    case Multiselect = 'multiselect';
    case RadioButton = 'radio_button';
    case CheckboxGroup = 'checkbox_group';
    case ImageUpload = 'image_upload';
    case DownloadableFile = 'downloadable_file';

    public function component(): string
    {
        $name = match ($this) {
            self::Text => 'sections.text',
            self::Textarea => 'sections.textarea',
            self::RichEditor => 'sections.rich-editor',
            self::Number => 'sections.number',
            self::Bool => 'sections.bool',
            self::Select => 'sections.select',
            self::Multiselect => 'sections.multiselect',
            self::RadioButton => 'sections.radio-button',
            self::CheckboxGroup => 'sections.checkbox-group',
            self::ImageUpload => 'sections.image-upload',
            self::DownloadableFile => 'sections.downloadable-file',
        };

        // If the component has been published to the app's views directory, use it directly.
        // Otherwise, fall back to the package's namespaced component.
        if (view()->exists('components.'.$name)) {
            return $name;
        }

        return 'dynamic-content::'.$name;
    }
}
