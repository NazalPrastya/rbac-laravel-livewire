@props([
    'title' => 'Title nih', 
    'subtitle' => null
])

<div class="flex flex-col ">
    <flux:heading class="text-xl lg:text-2xl xl:text-3xl font-bold">{{ $title }}</flux:heading>
    @if ($subtitle)
    <flux:text class="text-muted">{{ $subtitle }}</flux:text>
    @endif
</div>