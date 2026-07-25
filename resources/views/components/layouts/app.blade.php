<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cukupin — {{ $title ?? 'Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-paper text-ink font-sans antialiased">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-pine-dark text-cream/90 flex-shrink-0 p-6 hidden lg:flex lg:flex-col">
            <div class="font-display font-bold text-xl text-cream mb-10">Cukup<span class="text-kunyit">i</span>n</div>
            <nav class="space-y-1 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-cream/10 {{ request()->routeIs('dashboard') ? 'bg-cream/10' : '' }}">Dashboard</a>
                <a href="{{ route('items.index') }}" class="block px-3 py-2 rounded-lg hover:bg-cream/10 {{ request()->routeIs('items.*') ? 'bg-cream/10' : '' }}">Pencatatan Kebutuhan</a>
                <a href="{{ route('wallets.index') }}" class="block px-3 py-2 rounded-lg hover:bg-cream/10 {{ request()->routeIs('wallets.*') ? 'bg-cream/10' : '' }}">Dompet</a>
                <a href="{{ route('debts.index') }}" class="block px-3 py-2 rounded-lg hover:bg-cream/10 {{ request()->routeIs('debts.*') ? 'bg-cream/10' : '' }}">Utang & Piutang</a>
                <a href="{{ route('savings.index') }}" class="block px-3 py-2 rounded-lg hover:bg-cream/10 {{ request()->routeIs('savings.*') ? 'bg-cream/10' : '' }}">Target Tabungan</a>
                @if (auth()->user()?->isOwner())
                    <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-lg hover:bg-cream/10 {{ request()->routeIs('users.*') ? 'bg-cream/10' : '' }}">Kelola Pengguna</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg hover:bg-cream/10 {{ request()->routeIs('profile.*') ? 'bg-cream/10' : '' }}">Profil & Telegram</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <button class="text-sm text-cream/70 hover:text-cream">Logout</button>
            </form>
        </aside>

        <main class="flex-1 p-6 lg:p-10">
            <div class="max-w-6xl mx-auto">
                <h1 class="font-display text-2xl text-pine-dark mb-6">{{ $title ?? 'Dashboard' }}</h1>
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
