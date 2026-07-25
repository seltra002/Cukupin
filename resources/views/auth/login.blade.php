<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Cukupin</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper font-sans min-h-screen flex items-center justify-center p-6">
    <div class="bg-cream rounded-2xl border border-line p-8 w-full max-w-sm">
        <div class="font-display font-bold text-2xl text-pine-dark mb-6">Cukup<span class="text-kunyit">i</span>n</div>

        @if ($errors->any())
            <div class="bg-chili/10 text-chili text-sm rounded-lg p-3 mb-4">
                @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-mono text-ink-soft">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg border-line text-sm px-3 py-2">
            </div>
            <div>
                <label class="text-xs font-mono text-ink-soft">Password</label>
                <input type="password" name="password" required class="w-full rounded-lg border-line text-sm px-3 py-2">
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" class="rounded border-line"> Ingat saya
            </label>
            <button type="submit" class="w-full bg-kunyit text-pine-dark font-semibold text-sm py-2.5 rounded-lg">Login</button>
        </form>

        <p class="text-sm text-ink-soft text-center mt-5">
            Belum punya akun? <a href="{{ route('register') }}" class="text-pine font-semibold">Daftar sebagai Owner</a>
        </p>
    </div>
</body>
</html>
