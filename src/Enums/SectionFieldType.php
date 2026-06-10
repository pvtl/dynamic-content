<?php

declare(strict_types=1);

namespace Pvtl\DynamicContent\Enums;

enum SectionFieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
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
        return match ($this) {
            self::Text => 'sections.text',
            self::Textarea => 'sections.textarea',
            self::Number => 'sections.number',
            self::Bool => 'sections.bool',
            self::Select => 'sections.select',
            self::Multiselect => 'sections.multiselect',
            self::RadioButton => 'sections.radio-button',
            self::CheckboxGroup => 'sections.checkbox-group',
            self::ImageUpload => 'sections.image-upload',
            self::DownloadableFile => 'sections.downloadable-file',
        };
    }
}
