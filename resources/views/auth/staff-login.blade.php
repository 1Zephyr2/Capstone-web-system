<!-- Staff Login Gateway -->
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Staff Portal</title>
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

    <!-- Ambient Background Glows (Staff specific: Purple/Indigo accent) -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/5 rounded-full blur-[120px]"></div>
    </div>

    <!-- Navbar -->
    <nav class="relative z-50 w-full bg-[#0c1220] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <i class="bi bi-shield-lock text-indigo-500"></i>FURCARE <span class="text-indigo-400 font-normal text-sm ml-2 px-2 py-0.5 rounded-md bg-indigo-500/10 border border-indigo-500/20">STAFF</span>
            </a>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center relative z-10 py-12 px-6">
        <div class="w-full max-w-md bg-slate-900/40 backdrop-blur-md border border-slate-800/80 rounded-2xl p-8 shadow-2xl reveal-on-scroll opacity-0 translate-y-10 transition-all duration-1000 ease-out hover:border-slate-700 transition-all duration-300 hover:shadow-[0_0_20px_rgba(99,102,241,0.1)]">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Staff Authentication</h2>

            <form action="{{ route('staff.login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Staff Email</label>
                    <input type="email" name="email" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-1">Secure Password</label>
                    <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <button type="submit" class="w-full py-3 mt-4 font-bold text-sm text-white rounded-xl bg-indigo-600 hover:bg-indigo-500 transition-all duration-300 transform hover:-translate-y-1 shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40">
                    Access System
                </button>
            </form>
        </div>
    </main>

    <footer class="relative z-10 py-10 text-center border-t border-white/5">
        <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} FURCARE. Staff portal restricted.</p>
    </footer>

</body>
</html>
