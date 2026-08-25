@props([
    'title' => 'Title nih', 
    'subtitle' => null
])

<div class="flex flex-col ">
    <h1 class="text-xl lg:text-2xl xl:text-3xl font-bold">{{ $title }}</h1>
    @if ($subtitle)
    <h2 class="text-muted">{{ $subtitle }}</h2>
    @endif
</div>