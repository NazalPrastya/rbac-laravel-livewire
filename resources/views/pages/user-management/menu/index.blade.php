<x-layouts::app :title="__('Master Menu')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex flex-col gap-3">
            <x-title-page :title="__('Master Menu')" :subtitle="__('Kelola menu pada dashboard')"></x-title-page>
            <div class="flex justify-end ">
                <livewire:menu-form />
            </div>
        </div>

        <div class="w-full">
            <livewire:menu-table />
        </div>
    </div>
</x-layouts::app>
