@php
    $staffMembers    = App\Models\User::where('role', 'staff')->orderBy('name')->get();
    $owners          = App\Models\User::where('role', 'owner')->with('pets')->orderBy('name')->get();
    $groomingStyles  = App\Models\GroomingOption::where('type','style')->orderBy('name')->get();
    $groomingAddons  = App\Models\GroomingOption::where('type','addon')->orderBy('name')->get();
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById('panel-' + tabId).classList.remove('hidden');
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-900/50','text-white','border-indigo-500');
                btn.classList.add('text-indigo-300','border-transparent','hover:bg-indigo-900/20');
            });
            document.getElementById('btn-' + tabId).classList.add('bg-indigo-900/50','text-white','border-indigo-500');
            document.getElementById('btn-' + tabId).classList.remove('text-indigo-300','border-transparent');
        }

        function toggleModal(id, contentId) {
            const modal = document.getElementById(id), content = document.getElementById(contentId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden'); modal.classList.add('flex');
                setTimeout(() => { modal.classList.add('opacity-100'); content.classList.remove('scale-95','opacity-0'); content.classList.add('scale-100','opacity-100'); }, 10);
            } else {
                modal.classList.remove('opacity-100'); content.classList.remove('scale-100','opacity-100'); content.classList.add('scale-95','opacity-0');
                setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
            }
        }

        function confirmDelete(formId, msg) {
            if (confirm(msg)) document.getElementById(formId).submit();
        }

        // Set grooming add form type
        function openAddGrooming(type) {
            document.getElementById('grooming-type-input').value = type;
            document.getElementById('grooming-modal-title').innerText = type === 'style' ? 'Add Grooming Style' : 'Add Grooming Add-on';
            toggleModal('add-grooming-modal','add-grooming-modal-content');
        }

        document.addEventListener('DOMContentLoaded', () => {
            showTab('staff');
            @if(session('panel_tab')) showTab('{{ session('panel_tab') }}'); @endif
            @if(session('open_add_staff')) toggleModal('add-staff-modal','add-staff-modal-content'); @endif
        });
    </script>
</head>
<body class="bg-indigo-950 text-indigo-100 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#1e1b4b] backdrop-blur-md border-b border-white/10">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-rose-300 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-rose-500/20 border border-rose-500/30">ADMIN PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-indigo-300">
                <a href="{{ route('admin.dashboard') }}"    class="hover:text-white transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route('admin.directory') }}"    class="hover:text-white transition-all hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-white transition-all hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}"     class="hover:text-white transition-all hover:scale-105">Insights</a>
                <a href="{{ route('admin.panel') }}"        class="text-white transition-all bg-rose-900/30 px-3 py-1 rounded-lg border border-rose-500/30 ml-4 hover:bg-rose-900/50">Admin Panel</a>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-rose-900/30 hover:bg-rose-900/50 text-rose-300 transition-all">Logout</button>
            </form>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center gap-3">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 flex items-center gap-3">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        <header class="mb-10">
            <h1 class="text-3xl font-bold text-white">Admin Panel</h1>
            <p class="text-indigo-300 text-sm">System configuration and master controls.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-2">
                <button id="btn-staff"    onclick="showTab('staff')"    class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium border-l-4 transition-all"><i class="bi bi-person-badge mr-2"></i> Staff Management</button>
                <button id="btn-owners"   onclick="showTab('owners')"   class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium border-l-4 transition-all text-indigo-300 border-transparent hover:bg-indigo-900/20"><i class="bi bi-people mr-2"></i> Owner Accounts</button>
                <button id="btn-services" onclick="showTab('services')" class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium border-l-4 transition-all text-indigo-300 border-transparent hover:bg-indigo-900/20"><i class="bi bi-scissors mr-2"></i> Grooming Options</button>
                <button id="btn-settings" onclick="showTab('settings')" class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium border-l-4 transition-all text-indigo-300 border-transparent hover:bg-indigo-900/20"><i class="bi bi-gear mr-2"></i> System Settings</button>
            </div>

            <!-- Content -->
            <div class="lg:col-span-3 bg-indigo-900/20 border border-indigo-800/50 rounded-2xl p-8 min-h-[500px]">

                <!-- Staff -->
                <div id="panel-staff" class="tab-panel hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-white">Staff Management</h2>
                            <p class="text-indigo-400 text-sm">{{ $staffMembers->count() }} staff {{ Str::plural('member',$staffMembers->count()) }}</p>
                        </div>
                        <button onclick="toggleModal('add-staff-modal','add-staff-modal-content')"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-sm font-semibold transition-all hover:scale-105 text-white">
                            <i class="bi bi-plus-circle mr-1"></i> Add Staff
                        </button>
                    </div>
                    <div class="space-y-3">
                        @forelse($staffMembers as $staff)
                            <div class="bg-indigo-950/40 border border-indigo-800/50 rounded-xl p-4 flex items-center justify-between hover:border-indigo-600 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-violet-500/20 flex items-center justify-center text-violet-300 font-bold text-sm">{{ strtoupper(substr($staff->name,0,1)) }}</div>
                                    <div>
                                        <p class="font-semibold text-white">{{ $staff->name }}</p>
                                        <p class="text-xs text-indigo-400">{{ $staff->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-violet-500/20 text-violet-300 border border-violet-500/30">Staff</span>
                                    <form id="delete-staff-{{ $staff->id }}" method="POST" action="{{ route('admin.staff.destroy', $staff) }}">@csrf @method('DELETE')</form>
                                    <button onclick="confirmDelete('delete-staff-{{ $staff->id }}','Remove {{ addslashes($staff->name) }}?')"
                                            class="p-2 rounded-lg text-rose-400 hover:bg-rose-500/20 transition-all"><i class="bi bi-trash text-sm"></i></button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8"><i class="bi bi-person-badge text-4xl text-indigo-800 mb-3 block"></i><p class="text-indigo-500 italic text-sm">No staff accounts yet.</p></div>
                        @endforelse
                    </div>
                </div>

                <!-- Owners -->
                <div id="panel-owners" class="tab-panel hidden">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-white">Owner Accounts</h2>
                        <p class="text-indigo-400 text-sm">{{ $owners->count() }} registered {{ Str::plural('owner',$owners->count()) }}</p>
                    </div>
                    <div class="space-y-3">
                        @forelse($owners as $owner)
                            <div class="bg-indigo-950/40 border border-indigo-800/50 rounded-xl p-4 flex items-center justify-between hover:border-indigo-600 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-teal-500/20 flex items-center justify-center text-teal-300 font-bold text-sm">{{ strtoupper(substr($owner->name,0,1)) }}</div>
                                    <div>
                                        <p class="font-semibold text-white">{{ $owner->name }}</p>
                                        <p class="text-xs text-indigo-400">{{ $owner->email }}</p>
                                        <p class="text-xs text-indigo-500 mt-0.5">{{ $owner->pets->count() }} {{ Str::plural('pet',$owner->pets->count()) }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-teal-500/20 text-teal-300 border border-teal-500/30">Owner</span>
                            </div>
                        @empty
                            <p class="text-indigo-500 text-sm italic">No owners registered yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Grooming Options (Item 2) -->
                <div id="panel-services" class="tab-panel hidden">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-white">Grooming Options</h2>
                            <p class="text-indigo-400 text-sm">Manage available styles and add-ons shown to clients.</p>
                        </div>
                    </div>

                    <!-- Styles -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-white flex items-center gap-2"><i class="bi bi-scissors text-indigo-400"></i> Grooming Styles</h3>
                            <button onclick="openAddGrooming('style')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-xs font-semibold text-white transition-all hover:scale-105">
                                <i class="bi bi-plus mr-1"></i> Add Style
                            </button>
                        </div>
                        <div class="space-y-2">
                            @forelse($groomingStyles as $opt)
                                <div class="bg-indigo-950/40 border {{ $opt->is_active ? 'border-indigo-800/50' : 'border-slate-700/30 opacity-60' }} rounded-xl p-4 flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-white text-sm">{{ $opt->name }}
                                            @if(!$opt->is_active)<span class="ml-2 text-xs text-slate-500">(disabled)</span>@endif
                                        </p>
                                        @if($opt->description)<p class="text-xs text-indigo-400 mt-0.5">{{ $opt->description }}</p>@endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.grooming.toggle', $opt) }}">@csrf @method('PATCH')
                                            <button type="submit" class="px-3 py-1 rounded-lg text-xs {{ $opt->is_active ? 'bg-slate-700 text-slate-300 hover:bg-slate-600' : 'bg-indigo-700 text-indigo-200 hover:bg-indigo-600' }} transition-all">
                                                {{ $opt->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.grooming.destroy', $opt) }}" onsubmit="return confirm('Remove {{ addslashes($opt->name) }}?')">@csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/20 transition-all"><i class="bi bi-trash text-xs"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-indigo-500 text-sm italic py-3">No styles added yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Add-ons -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-white flex items-center gap-2"><i class="bi bi-plus-circle text-indigo-400"></i> Add-on Services</h3>
                            <button onclick="openAddGrooming('addon')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-xs font-semibold text-white transition-all hover:scale-105">
                                <i class="bi bi-plus mr-1"></i> Add Add-on
                            </button>
                        </div>
                        <div class="space-y-2">
                            @forelse($groomingAddons as $opt)
                                <div class="bg-indigo-950/40 border {{ $opt->is_active ? 'border-indigo-800/50' : 'border-slate-700/30 opacity-60' }} rounded-xl p-4 flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-white text-sm">{{ $opt->name }}
                                            @if(!$opt->is_active)<span class="ml-2 text-xs text-slate-500">(disabled)</span>@endif
                                        </p>
                                        @if($opt->description)<p class="text-xs text-indigo-400 mt-0.5">{{ $opt->description }}</p>@endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.grooming.toggle', $opt) }}">@csrf @method('PATCH')
                                            <button type="submit" class="px-3 py-1 rounded-lg text-xs {{ $opt->is_active ? 'bg-slate-700 text-slate-300 hover:bg-slate-600' : 'bg-indigo-700 text-indigo-200 hover:bg-indigo-600' }} transition-all">
                                                {{ $opt->is_active ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.grooming.destroy', $opt) }}" onsubmit="return confirm('Remove {{ addslashes($opt->name) }}?')">@csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/20 transition-all"><i class="bi bi-trash text-xs"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-indigo-500 text-sm italic py-3">No add-ons added yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div id="panel-settings" class="tab-panel hidden">
                    <div class="mb-6"><h2 class="text-xl font-bold text-white">System Settings</h2><p class="text-indigo-400 text-sm">General system information.</p></div>
                    <div class="space-y-4">
                        <div class="bg-indigo-950/40 border border-indigo-800/50 rounded-xl p-5">
                            <h3 class="font-semibold text-white text-sm uppercase tracking-widest mb-3">System Info</h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-indigo-900/30 rounded-lg p-3"><p class="text-indigo-400 text-xs mb-1">Laravel Version</p><p class="text-white font-medium">{{ app()->version() }}</p></div>
                                <div class="bg-indigo-900/30 rounded-lg p-3"><p class="text-indigo-400 text-xs mb-1">PHP Version</p><p class="text-white font-medium">{{ phpversion() }}</p></div>
                                <div class="bg-indigo-900/30 rounded-lg p-3"><p class="text-indigo-400 text-xs mb-1">Environment</p><p class="text-white font-medium">{{ app()->environment() }}</p></div>
                                <div class="bg-indigo-900/30 rounded-lg p-3"><p class="text-indigo-400 text-xs mb-1">App Name</p><p class="text-white font-medium">{{ config('app.name') }}</p></div>
                            </div>
                        </div>
                        <div class="bg-indigo-950/40 border border-indigo-800/50 rounded-xl p-5">
                            <h3 class="font-semibold text-white text-sm uppercase tracking-widest mb-3">Clinic Hours</h3>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                @foreach(\App\Models\Appointment::CLINIC_HOURS as $value => $label)
                                    <div class="flex items-center gap-2 bg-indigo-900/30 rounded-lg px-3 py-2">
                                        <i class="bi bi-clock text-indigo-400 text-xs"></i>
                                        <span class="text-white">{{ $label }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Staff Modal -->
    <div id="add-staff-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-indigo-950/90 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal('add-staff-modal','add-staff-modal-content')">
        <div id="add-staff-modal-content" class="bg-indigo-900 border border-indigo-700 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-white mb-6">Add New Staff Member</h2>
            <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
                @csrf
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-indigo-950 border border-indigo-700 rounded-xl px-4 py-3 text-white outline-none focus:border-indigo-400 transition-all" placeholder="e.g. Maria Santos"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-indigo-950 border border-indigo-700 rounded-xl px-4 py-3 text-white outline-none focus:border-indigo-400 transition-all" placeholder="staff@furcare.com"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-indigo-950 border border-indigo-700 rounded-xl px-4 py-3 text-white outline-none focus:border-indigo-400 transition-all" placeholder="Minimum 8 characters"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-indigo-950 border border-indigo-700 rounded-xl px-4 py-3 text-white outline-none focus:border-indigo-400 transition-all" placeholder="Repeat password"></div>
                @if($errors->any())
                    <div class="p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm space-y-1">
                        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                    </div>
                @endif
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleModal('add-staff-modal','add-staff-modal-content')" class="flex-1 px-4 py-3 rounded-xl bg-indigo-800 hover:bg-indigo-700 text-white font-semibold transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition-all hover:scale-[1.02]">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Grooming Option Modal -->
    <div id="add-grooming-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-indigo-950/90 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal('add-grooming-modal','add-grooming-modal-content')">
        <div id="add-grooming-modal-content" class="bg-indigo-900 border border-indigo-700 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 id="grooming-modal-title" class="text-xl font-bold text-white mb-6">Add Grooming Option</h2>
            <form method="POST" action="{{ route('admin.grooming.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" id="grooming-type-input" name="type" value="style">
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-1">Name</label>
                    <input type="text" name="name" required class="w-full bg-indigo-950 border border-indigo-700 rounded-xl px-4 py-3 text-white outline-none focus:border-indigo-400 transition-all" placeholder="e.g. Teddy Bear Cut"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-1">Description <span class="normal-case font-normal">(optional)</span></label>
                    <input type="text" name="description" class="w-full bg-indigo-950 border border-indigo-700 rounded-xl px-4 py-3 text-white outline-none focus:border-indigo-400 transition-all" placeholder="Brief description..."></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleModal('add-grooming-modal','add-grooming-modal-content')" class="flex-1 px-4 py-3 rounded-xl bg-indigo-800 hover:bg-indigo-700 text-white font-semibold transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition-all hover:scale-[1.02]">Add Option</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>