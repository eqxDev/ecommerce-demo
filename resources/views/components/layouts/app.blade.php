<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">

<header class="relative">
    <nav aria-label="Top">
        <!-- Top navigation -->
        <div class="bg-gray-900">
            <div class="mx-auto flex h-10 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

                <p class="flex-1 text-center text-sm font-medium text-white lg:flex-none">Get free delivery on orders
                    over £100</p>

                @if(Auth::guest())
                    <div class="hidden lg:flex lg:flex-1 lg:items-center lg:justify-end lg:space-x-6">
                        <a href="{{route('register')}}" wire:navigate class="text-sm font-medium text-white hover:text-gray-100">Create an account</a>
                        <span class="h-6 w-px bg-gray-600" aria-hidden="true"></span>
                        <a href="{{route('login')}}" wire:navigate class="text-sm font-medium text-white hover:text-gray-100">Sign in</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Secondary navigation -->
        <div class="bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="border-b border-gray-200">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Logo (lg+) -->
                        <div class="hidden lg:flex lg:items-center">
                            <a href="/" wire:navigate>
                                <span class="sr-only">Your Company</span>
                                <img class="h-8 w-auto"
                                     src="https://via.placeholder.com/200x80.png/f6f6f6?text=logo" alt="">
                            </a>
                        </div>

                        <!-- Logo (lg-) -->
                        <a href="/" wire:naviate class="lg:hidden">
                            <span class="sr-only">Your Company</span>
                            <img  src="https://via.placeholder.com/200x80.png/f6f6f6?text=logo" alt=""
                                 class="h-8 w-auto">
                        </a>

                        <div class="flex flex-1 items-center justify-end">
                            <div class="flex items-center lg:ml-8">
                               @if(Auth::check())
                                    <div class="flex space-x-8">
                                        <div class="flex">
                                            <a href="{{route('your-account')}}" wire:navigate class="-m-2 p-2 text-gray-400 hover:text-gray-500">
                                                <span class="sr-only">Account</span>
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                     stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>

                                    <span class="mx-4 h-6 w-px bg-gray-200 lg:mx-6" aria-hidden="true"></span>
                               @endif
                                <div class="flow-root">
                                    <livewire:basket/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

<main>
    {{ $slot }}
</main>

<x-footer/>
</body>
</html>
