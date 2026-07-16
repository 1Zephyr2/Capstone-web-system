<!-- Admin Login Gateway -->
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Portal</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <nav class="w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <i class="bi bi-shield-lock text-indigo-600"></i> FURCARE
                <span class="text-rose-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-rose-100 border border-rose-200">ADMIN PORTAL</span>
            </a>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center py-12 px-6">
        <div class="w-full max-w-md bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 mx-auto mb-4">
                <i class="bi bi-shield-lock text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-1 text-center">Admin Authentication</h2>
            <p class="text-gray-400 text-sm text-center mb-6">Restricted access for FURCARE administrators.</p>

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm space-y-1">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Admin Email</label>
                    <input type="email" name="email" required autofocus
                           class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Secure Password</label>
                    <input type="password" name="password" required
                           class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 outline-none transition-all">
                </div>
                <button type="submit" class="w-full py-3 mt-2 font-semibold text-sm text-white rounded-xl bg-indigo-600 hover:bg-indigo-700 transition-all shadow-sm">
                    Enter Admin Panel
                </button>
            </form>
        </div>
    </main>

    <footer class="py-8 text-center border-t border-gray-100">
        <p class="text-gray-400 text-sm">&copy; {{ date('Y') }} FURCARE. Admin access restricted.</p>
    </footer>

</body>
</html>
