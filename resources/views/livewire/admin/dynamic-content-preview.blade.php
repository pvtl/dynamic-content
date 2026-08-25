<div class="flex h-full min-h-0 flex-col bg-zinc-100 dark:bg-zinc-950">
    <div class="flex shrink-0 items-center justify-between gap-4 border-b border-zinc-200 bg-white px-5 py-3 pe-14 dark:border-zinc-700 dark:bg-zinc-900">
        <div>
            <flux:heading level="2" size="lg">{{ __('Preview') }}</flux:heading>
            <flux:text class="text-xs">{{ __('Preview the current sections across responsive layouts.') }}</flux:text>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-1">
            @foreach ($this->viewportOptions() as $value => $option)
                <flux:button wire:key="preview-viewport-{{ $value }}"
                    wire:click="setViewport('{{ $value }}')"
                    :variant="$viewport === $value ? 'primary' : 'ghost'"
                    size="sm"
                    :title="$option['width']">
                    {{ $option['label'] }}
                </flux:button>
            @endforeach
        </div>
    </div>

    <div class="min-h-0 flex-1 overflow-auto p-4">
        <div @class([
            'mx-auto h-full shrink-0 overflow-hidden bg-white shadow-lg ring-1 ring-black/10',
            'w-full' => $viewport === 'base',
            'w-[375px]' => $viewport === 'mobile',
            'w-[768px]' => $viewport === 'tablet',
            'w-[1280px]' => $viewport === 'desktop',
        ])>
            <iframe x-effect="$el.srcdoc = $wire.renderedDocument"
                wire:ignore
                title="{{ __('Dynamic content preview') }}"
                class="h-full w-full border-0 bg-white"></iframe>
        </div>
    </div>
</div>
