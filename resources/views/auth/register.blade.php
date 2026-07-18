<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Register</title>
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
            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-emerald-600 transition-colors">Login</a>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center py-12 px-6">
        <div class="w-full max-w-md bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-gray-900 mb-1 text-center">Create an account</h2>
            <p class="text-gray-400 text-sm text-center mb-6">Join FURCARE to book grooming for your pets.</p>

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Mobile Number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 0917 123 4567"
                           class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all">
                </div>
                <button type="submit" class="w-full py-3 mt-2 font-semibold text-sm text-white rounded-xl bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm">
                    Register
                </button>
            </form>
        </div>
    </main>

    <footer class="py-8 text-center border-t border-gray-100">
        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} FURCARE | Appointment System</p>
    </footer>

</body>
</html>
