<x-layouts::app :title="__('Master Role')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex flex-col gap-3">
            <x-title-page :title="__('Master Role')" :subtitle="__('Kelola role pengguna pada dashboard')"></x-title-page>
            <div class="flex justify-end">
                <livewire:role-form />
            </div>
        </div>

        <div class="w-full">
            <livewire:role-table />
        </div>
    </div>
</x-layouts::app>
