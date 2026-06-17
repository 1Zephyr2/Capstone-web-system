<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                        entry.target.classList.remove('opacity-0', 'translate-y-10');
                    }
                });
            }, { threshold: 0.1 });
            document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
        });
    </script>
</head>
<body class="bg-slate-950 text-slate-200 antialiased relative overflow-x-hidden min-h-screen flex flex-col">

    <!-- Ambient Background Glows -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- Navbar -->
    <!-- Navbar -->
    <nav class="relative z-50 w-full bg-[#0c1220] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <i class="bi bi-shield-check text-teal-500"></i>FURCARE
            </a>
            <a href="{{ route('register') }}" class="px-5 py-2 rounded-full text-sm font-medium text-slate-300 hover:text-teal-400 transition-colors">Register</a>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center relative z-10 py-12 px-6">
        <div class="w-full max-w-md bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-8 shadow-2xl reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(20,184,166,0.1)]">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Welcome back</h2>

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-teal-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-teal-500 outline-none">
                </div>
                <div class="flex items-center justify-between text-sm text-slate-400">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2 rounded border-slate-700 bg-slate-950 text-teal-600 focus:ring-teal-500">
                        Remember me
                    </label>
                </div>
                <button type="submit" class="w-full py-3 mt-4 font-bold text-sm text-white rounded-xl bg-teal-500 hover:bg-teal-400 transition-all duration-300 transform hover:-translate-y-1 shadow-lg shadow-teal-500/25 hover:shadow-teal-500/40">
                    Sign in
                </button>
            </form>
        </div>
    </main>

    <footer class="relative z-10 py-10 text-center border-t border-white/5">
        <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} FURCARE. All rights reserved.</p>
    </footer>

</body>
</html>
