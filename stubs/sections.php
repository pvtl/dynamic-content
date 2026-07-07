<?php

use Pvtl\DynamicContent\Enums\SectionFieldType;

/*
 * Section Schema
 * --------------
 * Each entry in this array defines a content section available in the dynamic content manager.
 *
 * Section keys:
 *   slug        (string)  - Unique identifier for the section type. Used as the DB slug.
 *   component   (string)  - Blade component name (relative to the directory defined in
 *                           dynamic_content.component_directory config). Used to render the
 *                           section on the frontend via <x-dynamic-component>.
 *   description (string)  - Human-readable description shown in the admin section picker.
 *   fields      (array)   - List of editable fields for this section (see Field keys below).
 *
 * Field keys:
 *   name        (string)           - Label shown in the admin UI.
 *   slug        (string)           - Key used to store and retrieve the field value in the
 *                                    content JSON column.
 *   description (string)           - Helper text shown below the field label in the admin UI.
 *   type        (SectionFieldType) - Field input type. Available types:
 *                                      Text, Textarea, RichEditor, Number, Bool, Select,
 *                                      Multiselect, RadioButton, CheckboxGroup, ImageUpload,
 *                                      DownloadableFile, Repeater
 *   class       (string)           - Tailwind classes applied to the field wrapper (e.g. 'w-1/2').
 *   default     (mixed)            - Default value used when a new section is added.
 *   validation  (array)            - Laravel validation rules applied on save.
 *   options     (array)            - Key/value pairs for Select, Multiselect, RadioButton, and
 *                                    CheckboxGroup fields. Empty array for all other types.
 *   fields      (array)            - Repeater fields only. List of nested field definitions
 *                                    (same Field keys as above) rendered for every repeater row.
 *                                    Any field type may be nested, including another Repeater.
 *                                    Stored content for a Repeater field is an associative array
 *                                    keyed by a generated row id — always iterate its values,
 *                                    never rely on numeric/sequential keys.
 *
 * Frontend rendering:
 *   Each section's component receives the stored field values as an $attrs array:
 *     <x-dynamic.my-section :attrs="$section->content" />
 *   Components live in resources/views/components/{component_directory}/.
 *   They must never query the database — all data is passed via $attrs.
 */

return [
    [
        'slug' => 'homepage-hero',
        'component' => 'homepage-hero',
        'description' => 'Hero banner displayed at the top of the homepage.',
        'fields' => [
            [
                'name' => 'Heading',
                'slug' => 'heading',
                'description' => 'Main headline text.',
                'type' => SectionFieldType::Text,
                'class' => 'w-1/2',
                'default' => '',
                'validation' => ['required', 'string', 'max:255'],
                'options' => [],
            ],
            [
                'name' => 'Background image',
                'slug' => 'background_image',
                'description' => 'Full-width hero background image.',
                'type' => SectionFieldType::ImageUpload,
                'class' => 'w-1/2',
                'default' => null,
                'validation' => ['nullable', 'image', 'max:2048'],
                'options' => [],
            ],
            [
                'name' => 'Body',
                'slug' => 'body',
                'description' => 'Supporting paragraph beneath the headline.',
                'type' => SectionFieldType::RichEditor,
                'class' => 'w-full',
                'default' => '',
                'validation' => ['required', 'string'],
                'options' => [],
            ],
            [
                'name' => 'Layout',
                'slug' => 'layout',
                'description' => 'Choose how content is aligned in the hero.',
                'type' => SectionFieldType::Select,
                'class' => 'w-1/2',
                'default' => 'left',
                'validation' => ['required', 'string', 'in:left,center,right'],
                'options' => [
                    'left' => 'Left',
                    'center' => 'Center',
                    'right' => 'Right',
                ],
            ],
            [
                'name' => 'Highlights',
                'slug' => 'highlights',
                'description' => 'Repeatable highlight cards displayed beneath the hero.',
                'type' => SectionFieldType::Repeater,
                'class' => 'w-full',
                'default' => [],
                'validation' => ['array'],
                'options' => [],
                'fields' => [
                    [
                        'name' => 'Image',
                        'slug' => 'image',
                        'description' => 'Highlight image.',
                        'type' => SectionFieldType::ImageUpload,
                        'class' => 'w-1/2',
                        'default' => null,
                        'validation' => ['nullable', 'image', 'max:2048'],
                        'options' => [],
                    ],
                    [
                        'name' => 'Description',
                        'slug' => 'description',
                        'description' => 'Rich text content for this highlight.',
                        'type' => SectionFieldType::RichEditor,
                        'class' => 'w-full',
                        'default' => '',
                        'validation' => ['required', 'string'],
                        'options' => [],
                    ],
                ],
            ],
        ],
    ],
];
