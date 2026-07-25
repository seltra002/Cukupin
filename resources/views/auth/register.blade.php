<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Cukupin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper font-sans min-h-screen flex items-center justify-center p-6">
    <div class="bg-cream rounded-2xl border border-line p-8 w-full max-w-sm">
        <div class="font-display font-bold text-2xl text-pine-dark mb-1">Cukup<span class="text-kunyit">i</span>n</div>
        <p class="text-sm text-ink-soft mb-6">Daftar sebagai Owner & bikin rumah tangga baru.</p>

        @if ($errors->any())
            <div class="bg-chili/10 text-chili text-sm rounded-lg p-3 mb-4">
                @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-mono text-ink-soft">Nama Kamu</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full rounded-lg border-line text-sm px-3 py-2">
            </div>
            <div>
                <label class="text-xs font-mono text-ink-soft">Nama Rumah Tangga</label>
                <input type="text" name="household_name" value="{{ old('household_name') }}" placeholder="Keluarga Budi" required class="w-full rounded-lg border-line text-sm px-3 py-2">
            </div>
            <div>
                <label class="text-xs font-mono text-ink-soft">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-line text-sm px-3 py-2">
            </div>
            <div>
                <label class="text-xs font-mono text-ink-soft">Password</label>
                <input type="password" name="password" required class="w-full rounded-lg border-line text-sm px-3 py-2">
            </div>
            <div>
                <label class="text-xs font-mono text-ink-soft">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border-line text-sm px-3 py-2">
            </div>
            <button type="submit" class="w-full bg-kunyit text-pine-dark font-semibold text-sm py-2.5 rounded-lg">Daftar</button>
        </form>

        <p class="text-sm text-ink-soft text-center mt-5">
            Udah punya akun? <a href="{{ route('login') }}" class="text-pine font-semibold">Login</a>
        </p>
    </div>
</body>
</html>
