<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name', 'Library Management') }} - Library Management</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        @auth
            <div class="min-h-screen lg:flex">
                <aside class="flex w-full shrink-0 flex-col bg-slate-950 text-white lg:fixed lg:inset-y-0 lg:flex lg:w-72">
                    <div class="flex items-center gap-3 border-b border-white/10 px-6 py-6">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500 text-xl font-bold text-white shadow-lg shadow-indigo-950/40">L</span>
                        <span>
                            <span class="block text-sm font-bold tracking-tight">Scott Library</span>
                            <span class="block text-xs text-white/50">Management portal</span>
                        </span>
                    </div>

                    

                    <nav class="flex flex-1 flex-row gap-1 overflow-x-auto p-4 lg:flex-col">
                        <p class="hidden px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.2em] text-white/40 lg:block">Workspace</p>
                        @foreach ([
                            ['route' => 'dashboard', 'label' => 'Overview', 'icon' => '⌂', 'active' => request()->routeIs('dashboard')],
                            ['route' => 'books.index', 'label' => 'Books', 'icon' => '▦', 'active' => request()->routeIs('books.*')],
                            ['route' => 'categories.index', 'label' => 'Categories', 'icon' => '◈', 'active' => request()->routeIs('categories.*')],
                            ['route' => 'members.index', 'label' => 'Members', 'icon' => '◎', 'active' => request()->routeIs('members.*')],
                            ['route' => 'loans.index', 'label' => 'Loans', 'icon' => '↗', 'active' => request()->routeIs('loans.*')],
                        ] as $item)
                            <a href="{{ route($item['route']) }}"
                               data-sidebar-link
                               data-sidebar-path="{{ parse_url(route($item['route']), PHP_URL_PATH) ?: '/' }}"
                               class="sidebar-link {{ $item['active'] ? 'sidebar-link-active' : 'text-white/60' }}"
                               @if ($item['active']) aria-current="page" @endif>
                                <span class="flex h-6 w-6 items-center justify-center text-base">{{ $item['icon'] }}</span>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="border-t border-white/10 p-4">
                        <div class="flex items-center gap-2">
                            <div class="flex min-w-0 flex-1 items-center gap-3 rounded-2xl bg-white/5 px-3 py-3 ring-1 ring-white/10">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-400/20 font-bold text-indigo-200">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                                    <p class="flex items-center gap-1.5 text-xs text-white/50"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Scott Library</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex h-12 w-20 shrink-0 items-center justify-center rounded-xl text-lg font-semibold text-white/60 transition hover:bg-white/10 hover:text-white" aria-label="Log out" title="Log out">
                                    ↪
                                </button>
                            </form>
                            </div>

                        </div>
                    </div>
                </aside>

                <div class="min-w-0 flex-1 lg:ml-72">
                    <header class="flex items-center justify-between border-b border-slate-200/80 bg-white/90 px-5 py-4 backdrop-blur lg:px-8">
                        <div>
                            <p data-workspace-title class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">{{ $title ?? 'Library workspace' }}</p>
                            <p class="mt-1 text-sm text-slate-400">{{ now()->format('l, M d, Y') }}</p>
                        </div>
                        <div class="hidden items-center gap-3 sm:flex">
                            <span class="flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                System healthy
                            </span>
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-sm font-bold text-slate-700">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </div>
                    </header>
                    <main class="mx-auto max-w-7xl px-5 py-8 lg:px-10 lg:py-10">
                        @if (session('success'))
                            <div class="status-alert status-alert-success" role="status" data-status-alert>
                                <span class="status-alert-icon" aria-hidden="true">✓</span>
                                <div class="min-w-0 flex-1">
                                    <p class="status-alert-title">Success</p>
                                    <p class="status-alert-message">{{ session('success') }}</p>
                                </div>
                                <button type="button" class="status-alert-close" data-dismiss-alert aria-label="Dismiss notification">×</button>
                                <span class="status-alert-progress" aria-hidden="true"></span>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="status-alert status-alert-error" role="alert" data-status-alert>
                                <span class="status-alert-icon" aria-hidden="true">!</span>
                                <div class="min-w-0 flex-1">
                                    <p class="status-alert-title">Something went wrong</p>
                                    <p class="status-alert-message">{{ session('error') }}</p>
                                </div>
                                <button type="button" class="status-alert-close" data-dismiss-alert aria-label="Dismiss notification">×</button>
                                <span class="status-alert-progress" aria-hidden="true"></span>
                            </div>
                        @endif
                        <div id="confirm-dialog" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/40 p-5 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="confirm-dialog-title">
                            <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl">
                                <div class="flex items-start gap-4">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-xl font-bold text-red-600">!</span>
                                    <div>
                                        <h2 id="confirm-dialog-title" class="text-lg font-bold text-slate-900">Please confirm</h2>
                                        <p id="confirm-dialog-message" class="mt-1 text-sm leading-6 text-slate-500"></p>
                                    </div>
                                </div>
                                <div class="mt-6 flex justify-end gap-3">
                                    <button type="button" class="btn-secondary" data-close-confirm-dialog>Cancel</button>
                                    <button type="button" class="btn-danger" data-confirm-action>Delete</button>
                                </div>
                            </div>
                        </div>
                        {{ $slot }}
                    </main>
                </div>
            </div>
        @else
            <main class="min-h-screen px-5 py-8">
                {{ $slot }}
            </main>
        @endauth
    </body>
</html>
