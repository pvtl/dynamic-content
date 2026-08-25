@foreach ($sections as $section)
    @php $sectionConfig = $sectionConfigs[$section['slug']] ?? null @endphp

    @if ($sectionConfig)
        <x-dynamic-component
            :component="$componentDirectory.'.'.$sectionConfig['component']"
            :attrs="$section['fields'] ?? []" />
    @endif
@endforeach
