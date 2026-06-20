<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        function showTab(tabId) {
            // Update Tab Title
            const titles = {
                'staff': 'Staff Management',
                'services': 'Services & Pricing',
                'settings': 'System Settings'
            };
            document.getElementById('tab-title').innerText = titles[tabId];

            // Update Sidebar State
            document.querySelectorAll('button[onclick^="showTab"]').forEach(btn => {
                btn.classList.remove('bg-indigo-900/30', 'text-white', 'border-indigo-500');
                btn.classList.add('hover:bg-indigo-900/20', 'text-indigo-300', 'border-transparent');
            });
            event.currentTarget.classList.add('bg-indigo-900/30', 'text-white', 'border-indigo-500');
            event.currentTarget.classList.remove('hover:bg-indigo-900/20', 'text-indigo-300', 'border-transparent');

            // Load Content
            const content = document.getElementById('tab-content');
            const title = document.getElementById('tab-title');

            // Fade out
            content.classList.remove('opacity-100');
            content.classList.add('opacity-0');
            title.classList.add('opacity-0');

            setTimeout(() => {
                // Change content
                if (tabId === 'staff') {
                    content.innerHTML = `
                        <div class="space-y-4 animate-in fade-in duration-500">
                            <div class="flex justify-between items-center mb-6">
                                <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-sm font-semibold transition-all hover:scale-105 active:scale-95">+ Add New Staff</button>
                            </div>
                            <div class="bg-indigo-950/30 p-4 rounded-xl border border-indigo-800/50 flex justify-between items-center hover:border-indigo-500/50 transition-all hover:translate-x-1">
                                <div><p class="font-bold">John Staff</p><p class="text-xs text-indigo-400">staff@furcare.com</p></div>
                                <div class="space-x-2">
                                    <button class="text-indigo-400 hover:text-white transition-colors"><i class="bi bi-pencil"></i></button>
                                    <button class="text-rose-400 hover:text-white transition-colors"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (tabId === 'services') {
                    content.innerHTML = `
                        <div class="space-y-4 animate-in fade-in duration-500">
                            <div class="bg-indigo-950/30 p-4 rounded-xl border border-indigo-800/50 flex justify-between items-center hover:border-indigo-500/50 transition-all hover:translate-x-1">
                                <div><p class="font-bold">Full Grooming</p><p class="text-xs text-indigo-400">Price: $50.00</p></div>
                                <button class="text-indigo-400 hover:text-white transition-colors"><i class="bi bi-pencil"></i></button>
                            </div>
                            <div class="bg-indigo-950/30 p-4 rounded-xl border border-indigo-800/50 flex justify-between items-center hover:border-indigo-500/50 transition-all hover:translate-x-1">
                                <div><p class="font-bold">Bath & Brush</p><p class="text-xs text-indigo-400">Price: $30.00</p></div>
                                <button class="text-indigo-400 hover:text-white transition-colors"><i class="bi bi-pencil"></i></button>
                            </div>
                        </div>
                    `;
                } else {
                    content.innerHTML = '<p class="text-indigo-300 animate-in fade-in duration-500">System configuration options will be displayed here.</p>';
                }

                // Fade in
                content.classList.remove('opacity-0');
                content.classList.add('opacity-100');
                title.classList.remove('opacity-0');
            }, 300);
        }
    </script>
</head>
<body class="bg-indigo-950 text-indigo-100 antialiased min-h-screen">

    <!-- Navbar -->
    <nav class="relative z-50 w-full bg-[#1e1b4b] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <i class="bi bi-shield-lock text-indigo-400"></i> FURCARE Admin
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-indigo-300">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Dashboard</a>
                <a href="{{ route('admin.directory') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}" class="hover:text-white transition-all duration-300 hover:scale-105">Insights</a>
                <a href="{{ route('admin.panel') }}" class="text-white hover:text-white transition-all duration-300 bg-rose-900/30 px-3 py-1 rounded-lg border border-rose-500/30 ml-4 hover:bg-rose-900/50 hover:scale-105 active:scale-95">Admin Panel</a>
            </div>
            <div class="flex items-center gap-4">
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-5 py-2 rounded-full text-sm bg-rose-900/30 hover:bg-rose-900/50 text-rose-300 transition-all duration-300 shadow-lg hover:shadow-rose-900/10">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
        <header class="mb-12">
            <h1 class="text-3xl font-bold text-white">Admin Panel</h1>
            <p class="text-indigo-300 text-sm">System configuration and master controls.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Navigation -->
            <div class="lg:col-span-1 space-y-2">
                <button onclick="showTab('staff')" class="w-full text-left px-4 py-3 rounded-xl bg-indigo-900/30 text-white font-medium border-l-4 border-indigo-500">Staff Management</button>
                <button onclick="showTab('services')" class="w-full text-left px-4 py-3 rounded-xl hover:bg-indigo-900/20 text-indigo-300 font-medium border-l-4 border-transparent hover:border-indigo-500/50 transition-all">Services & Pricing</button>
                <button onclick="showTab('settings')" class="w-full text-left px-4 py-3 rounded-xl hover:bg-indigo-900/20 text-indigo-300 font-medium border-l-4 border-transparent hover:border-indigo-500/50 transition-all">System Settings</button>
            </div>

            <!-- Content Area -->
            <div class="lg:col-span-3 bg-indigo-900/20 border border-indigo-800/50 rounded-2xl p-8 min-h-[500px]">
                <h2 id="tab-title" class="text-xl font-bold text-white mb-6 transition-all duration-300">Staff Management</h2>
                <div id="tab-content" class="text-indigo-200 transition-opacity duration-300 ease-in-out opacity-100">
                    <!-- Dynamic content will load here -->
                    <p>Select a management tool from the sidebar to begin.</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
