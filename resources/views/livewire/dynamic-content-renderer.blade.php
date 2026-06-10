<div>
    @foreach ($this->sections as $section)
        @php $config = $this->sectionConfigs[$section->slug] ?? null @endphp
        @if ($config)
            <x-dynamic-component
                :component="$this->component($config['component'])"
                :attrs="$section->content" />
        @endif
    @endforeach
</div>
