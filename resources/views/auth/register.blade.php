<x-layouts.app title="Create account">

    {{-- Same card as the login page: black brand panel on the left, form on the right --}}
    <div class="mx-auto grid min-h-[calc(100vh-13rem)] max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/60 lg:grid-cols-[1.05fr_0.95fr]">

        {{-- Left panel (hidden on phones and tablets) --}}
        <div class="relative hidden overflow-hidden bg-black p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-20 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>
            <div class="relative">
                <div class="mb-12 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-2xl font-bold ring-1 ring-white/20">S</div>
                <p class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-white/70">Scott Library</p>
                <h1 class="max-w-md text-4xl font-bold leading-tight tracking-tight">A calmer way to manage your collection.</h1>
                <p class="mt-5 max-w-sm text-base leading-7 text-white/70">Keep every book, member, and loan organized from one simple workspace.</p>
            </div>
            <div class="relative flex items-center gap-3 text-sm text-white/70">
                <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                Your library workspace is ready
            </div>
        </div>

        {{-- Right side: the registration form --}}
        <div class="flex items-center p-7 sm:p-12">
            <div class="w-full">
                <div class="mb-8">
                    {{-- Small logo, only shown when the left panel is hidden --}}
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-black font-bold text-white lg:hidden">L</div>
                    <p class="mb-2 text-sm font-semibold uppercase tracking-[0.18em] text-black">Get started</p>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-900">Create your account</h2>
                    <p class="mt-2 text-sm text-slate-500">Set up access to your library workspace.</p>
                </div>

                <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Full name</label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name') }}" autofocus autocomplete="name" placeholder="Your name"
                               class="field">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email address</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com"
                               class="field">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                        <input type="password" id="password" name="password"
                               autocomplete="new-password" placeholder="At least 8 characters"
                               class="field">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Confirm password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               autocomplete="new-password" placeholder="Repeat your password"
                               class="field">
                    </div>

                    <button type="submit" class="btn-primary w-full">
                        Create account
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-slate-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-black underline underline-offset-4 hover:text-slate-600">SignIn</a>
                </p>
            </div>
        </div>
    </div>

</x-layouts.app>