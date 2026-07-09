<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Login</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <nav class="w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-lg font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-7 h-7" alt="Logo"> FURCARE
            </a>
            <a href="{{ route('register') }}" class="text-sm font-medium text-gray-500 hover:text-emerald-600 transition-colors">Register</a>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center py-12 px-6">
        <div class="w-full max-w-md bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-gray-900 mb-1 text-center">Welcome back</h2>
            <p class="text-gray-400 text-sm text-center mb-6">Log in to manage your pets and appointments.</p>

            @if(session('status'))
                <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-emerald-600 hover:text-emerald-700 font-medium">Forgot password?</a>
                    @endif
                </div>
                <button type="submit" class="w-full py-3 mt-2 font-semibold text-sm text-white rounded-xl bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm">
                    Sign in
                </button>
            </form>
        </div>
    </main>

    <footer class="py-8 text-center border-t border-gray-100">
        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} FURCARE. All rights reserved.</p>
    </footer>

</body>
</html>
