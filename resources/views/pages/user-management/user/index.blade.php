<x-layouts::app :title="__('User')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex flex-col gap-3">
            <x-title-page :title="__('User Management')" :subtitle="__('Kelola user')"></x-title-page>
            <div class="flex justify-end">
                <livewire:user-form />
            </div>
        </div>

        <div class="w-full">
            <livewire:user-table />
        </div>
    </div>
</x-layouts::app>
