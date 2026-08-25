<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header class="bg-transparent">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            @php
                $menus = auth()->user()->accessibleMenus();
                $currentPath = request()->path();
            @endphp

            <flux:sidebar.nav>
                @foreach ($menus->whereNull('parent_id') as $menu)
                    @php($children = $menus->where('parent_id', $menu->id))

                    @if ($children->isNotEmpty())
                        <flux:sidebar.group expandable heading="{{ $menu->menu_name }}" class="grid">
                            <x-slot:icon>
                                <iconify-icon icon="{{ $menu->icon ?: 'lucide:folder' }}" class="size-4" aria-hidden="true"></iconify-icon>
                            </x-slot:icon>

                            @foreach ($children as $child)
                                <flux:sidebar.item
                                    href="{{ $child->url }}"
                                    :current="$currentPath === ltrim($child->url, '/')"
                                    wire:navigate
                                >
                                    {{ $child->menu_name }}
                                </flux:sidebar.item>
                            @endforeach
                        </flux:sidebar.group>
                    @else
                        <flux:sidebar.item
                            href="{{ $menu->url }}"
                            :current="$currentPath === ltrim($menu->url, '/')"
                            wire:navigate
                        >
                            <x-slot:icon>
                                <iconify-icon icon="{{ $menu->icon ?: 'lucide:circle' }}" class="size-4" aria-hidden="true"></iconify-icon>
                            </x-slot:icon>
                            {{ $menu->menu_name }}
                        </flux:sidebar.item>
                    @endif
                @endforeach
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
