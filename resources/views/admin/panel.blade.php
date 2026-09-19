@php
    $staffMembers    = App\Models\User::where('role', 'staff')->orderBy('name')->get();
    $owners          = App\Models\User::where('role', 'owner')->with('pets')->orderBy('name')->get();
    $groomingStyles  = App\Models\GroomingOption::where('type','style')->orderBy('name')->get();
    $groomingAddons  = App\Models\GroomingOption::where('type','addon')->orderBy('name')->get();
    $bookingServices = App\Models\Service::notArchived()->orderBy('category')->orderBy('name')->get()->groupBy('category');
    $archivedServices = App\Models\Service::archived()->orderBy('name')->get();
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html { font-size: 112%; }</style>
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
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById('panel-' + tabId).classList.remove('hidden');
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-50','text-indigo-700','border-indigo-500');
                btn.classList.add('text-gray-500','border-transparent','hover:bg-gray-50');
            });
            document.getElementById('btn-' + tabId).classList.add('bg-indigo-50','text-indigo-700','border-indigo-500');
            document.getElementById('btn-' + tabId).classList.remove('text-gray-500','border-transparent');
        }

        function toggleServiceCategory(id) {
            document.getElementById('svc-panel-' + id).classList.toggle('hidden');
            document.getElementById('svc-chevron-' + id).classList.toggle('rotate-180');
        }
        function toggleModal(id, contentId) {
            const modal = document.getElementById(id), content = document.getElementById(contentId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden'); modal.classList.add('flex');
                setTimeout(() => { modal.classList.add('opacity-100'); content.classList.remove('scale-95','opacity-0'); content.classList.add('scale-100','opacity-100'); }, 10);
            } else {
                modal.classList.remove('opacity-100'); content.classList.remove('scale-100','opacity-100'); content.classList.add('scale-95','opacity-0');
                setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);            }
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

        function openEditService(id, name, category, prices) {
            document.getElementById('edit-service-form').action = `/admin/services/${id}`;
            document.getElementById('edit-service-name').value = name;
            document.getElementById('edit-service-category').value = category;

            const sizes = ['XS','S','M','L','XL','G'];
            if (prices && prices.flat !== undefined) {
                setEditPricingMode('flat');
                document.getElementById('edit-price-flat').value = prices.flat;
            } else {
                setEditPricingMode('size');
                sizes.forEach(s => {
                    document.getElementById('edit-price-' + s).value = (prices && prices[s] !== undefined) ? prices[s] : '';
                });
            }

            toggleModal('edit-service-modal','edit-service-modal-content');
        }

        function setAddPricingMode(mode) {
            document.getElementById('add-pricing-mode').value = mode;
            document.getElementById('add-size-prices').classList.toggle('hidden', mode !== 'size');
            document.getElementById('add-flat-price').classList.toggle('hidden', mode !== 'flat');
            togglePricingModeButtons('add', mode);
        }

        function setEditPricingMode(mode) {
            document.getElementById('edit-pricing-mode').value = mode;
            document.getElementById('edit-size-prices').classList.toggle('hidden', mode !== 'size');
            document.getElementById('edit-flat-price').classList.toggle('hidden', mode !== 'flat');
            togglePricingModeButtons('edit', mode);
        }

        function togglePricingModeButtons(prefix, mode) {
            const sizeBtn = document.getElementById(prefix + '-mode-btn-size');
            const flatBtn = document.getElementById(prefix + '-mode-btn-flat');
            [sizeBtn, flatBtn].forEach(btn => {
                btn.classList.remove('bg-white','shadow','text-gray-900');
                btn.classList.add('text-gray-500');
            });
            const active = mode === 'size' ? sizeBtn : flatBtn;
            active.classList.add('bg-white','shadow','text-gray-900');
            active.classList.remove('text-gray-500');
        }

        document.addEventListener('DOMContentLoaded', () => {
            showTab('staff');
            @if(session('panel_tab')) showTab('{{ session('panel_tab') }}'); @endif
            @if(session('open_add_staff')) toggleModal('add-staff-modal','add-staff-modal-content'); @endif
        });
    </script>
    <script>function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }</script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-gray-900">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
                <span class="text-rose-700 font-normal text-xs ml-2 px-2 py-0.5 rounded-md bg-rose-100 border border-rose-200">ADMIN PORTAL</span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-500">
                <a href="{{ route('admin.dashboard') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Dashboard</a>
                <a href="{{ route('admin.directory') }}"    class="hover:text-gray-900 transition-all hover:scale-105">Pets</a>
                <a href="{{ route('admin.appointments') }}" class="hover:text-gray-900 transition-all hover:scale-105">Appointments</a>
                <a href="{{ route('admin.insights') }}"     class="hover:text-gray-900 transition-all hover:scale-105">Insights</a>
                <a href="{{ route('admin.panel') }}"        class="text-rose-700 font-semibold transition-all bg-rose-50 px-3 py-1 rounded-lg border border-rose-200 ml-4 hover:bg-rose-100">Admin Panel</a>
            </div>
            @include('components.notification-bell', ['notifRoutePrefix' => 'admin.'])
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0 hidden md:block">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-full text-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 transition-all">Logout</button>
            </form>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </nav>
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-house"></i> Dashboard</a>
            <a href="{{ route('admin.directory') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><svg class="inline w-[1em] h-[1em]" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg> Pets</a>
            <a href="{{ route('admin.appointments') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-calendar-event"></i> Appointments</a>
            <a href="{{ route('admin.insights') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-graph-up"></i> Insights</a>
            <a href="{{ route('admin.panel') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-gear"></i> Admin Panel</a>
            <form action="{{ route('admin.logout') }}" method="POST" >
                @csrf
                <button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
    </div>

    <main class="container mx-auto px-6 py-12">

        @if(session('success'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-6 py-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            </div>
        @endif

        <header class="mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Admin Panel</h1>
            <p class="text-gray-500 text-sm">System configuration and master controls.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-2">
                <button id="btn-staff"    onclick="showTab('staff')"    class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium border-l-4 transition-all"><i class="bi bi-person-badge mr-2"></i> Staff Management</button>
                <button id="btn-owners"   onclick="showTab('owners')"   class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium border-l-4 transition-all text-gray-500 border-transparent hover:bg-gray-50"><i class="bi bi-people mr-2"></i> Owner Accounts</button>
                <button id="btn-services" onclick="showTab('services')" class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium border-l-4 transition-all text-gray-500 border-transparent hover:bg-gray-50"><i class="bi bi-scissors mr-2"></i> Grooming Options</button>
                <button id="btn-booking-services" onclick="showTab('booking-services')" class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium border-l-4 transition-all text-gray-500 border-transparent hover:bg-gray-50"><i class="bi bi-list-check mr-2"></i> Booking Services</button>
                <button id="btn-settings" onclick="showTab('settings')" class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium border-l-4 transition-all text-gray-500 border-transparent hover:bg-gray-50"><i class="bi bi-gear mr-2"></i> System Settings</button>
            </div>

            <!-- Content -->
            <div class="lg:col-span-3 bg-white border border-gray-200 rounded-2xl p-8 min-h-[500px]">

                <!-- Staff -->
                <div id="panel-staff" class="tab-panel hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Staff Management</h2>
                            <p class="text-gray-500 text-sm">{{ $staffMembers->count() }} staff {{ Str::plural('member',$staffMembers->count()) }}</p>
                        </div>
                        <button onclick="toggleModal('add-staff-modal','add-staff-modal-content')"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-sm font-semibold transition-all hover:scale-105 text-white">
                            <i class="bi bi-plus-circle mr-1"></i> Add Staff
                        </button>
                    </div>
                    <div class="space-y-3">
                        @forelse($staffMembers as $staff)
                            <div class="bg-gray-50/40 border border-gray-200 rounded-xl p-4 flex items-center justify-between hover:border-indigo-300 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-700 font-bold text-sm">{{ strtoupper(substr($staff->name,0,1)) }}</div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $staff->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $staff->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded-full text-xs bg-violet-100 text-violet-700 border border-violet-200">Staff</span>
                                    <form id="delete-staff-{{ $staff->id }}" method="POST" action="{{ route('admin.staff.destroy', $staff) }}">@csrf @method('DELETE')</form>
                                    <button onclick="confirmDelete('delete-staff-{{ $staff->id }}','Remove {{ addslashes($staff->name) }}?')"
                                            class="p-2 rounded-lg text-rose-600 hover:bg-rose-100 transition-all"><i class="bi bi-trash text-sm"></i></button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8"><i class="bi bi-person-badge text-4xl text-gray-300 mb-3 block"></i><p class="text-gray-400 italic text-sm">No staff accounts yet.</p></div>
                        @endforelse
                    </div>
                </div>

                <!-- Owners -->
                <div id="panel-owners" class="tab-panel hidden">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Owner Accounts</h2>
                        <p class="text-gray-500 text-sm">{{ $owners->count() }} registered {{ Str::plural('owner',$owners->count()) }}</p>
                    </div>
                    <div class="space-y-3">
                        @forelse($owners as $owner)
                            <div class="bg-gray-50/40 border border-gray-200 rounded-xl p-4 flex items-center justify-between hover:border-indigo-300 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold text-sm">{{ strtoupper(substr($owner->name,0,1)) }}</div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $owner->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $owner->email }}</p>
                                        @if($owner->phone)
                                            <p class="text-xs text-gray-500"><i class="bi bi-telephone mr-1"></i>{{ $owner->phone }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $owner->pets->count() }} {{ Str::plural('pet',$owner->pets->count()) }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-teal-100 text-teal-700 border border-teal-200">Owner</span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm italic">No owners registered yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Grooming Options -->
                <div id="panel-services" class="tab-panel hidden">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Grooming Options</h2>
                            <p class="text-gray-500 text-sm">Manage styles and add-ons shown to clients. Upload images for each.</p>
                        </div>
                    </div>

                    <!-- Styles -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <i class="bi bi-scissors text-gray-500"></i> Grooming Styles
                            </h3>
                            <button onclick="openAddGrooming('style')"
                                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-xs font-semibold text-white transition-all hover:scale-105">
                                <i class="bi bi-plus mr-1"></i> Add Style
                            </button>
                        </div>
                        <div class="space-y-3">
                            @forelse($groomingStyles as $opt)
                                <div class="bg-gray-50/40 border {{ $opt->is_active ? 'border-gray-200' : 'border-gray-300/30 opacity-60' }} rounded-xl p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="shrink-0">
                                            @if($opt->image)
                                                <img src="{{ asset('storage/' . $opt->image) }}"
                                                     alt="{{ $opt->name }}"
                                                     class="w-16 h-16 rounded-xl object-cover border border-gray-300">
                                            @else
                                                <div class="w-16 h-16 rounded-xl bg-white border border-gray-200 flex items-center justify-center">
                                                    <i class="bi bi-scissors text-gray-400 text-xl"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm">
                                                {{ $opt->name }}
                                                @if(!$opt->is_active)<span class="ml-2 text-xs text-gray-400">(disabled)</span>@endif
                                            </p>
                                            @if($opt->description)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $opt->description }}</p>
                                            @endif

                                            <form method="POST" action="{{ route('admin.grooming.image', $opt) }}"
                                                  enctype="multipart/form-data" class="mt-2 flex items-center gap-2">
                                                @csrf
                                                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 cursor-pointer text-xs text-indigo-700 transition-all">
                                                    <i class="bi bi-cloud-upload"></i>
                                                    {{ $opt->image ? 'Replace Image' : 'Upload Image' }}
                                                    <input type="file" name="image" accept="image/*" class="hidden"
                                                           onchange="this.closest('form').submit()">
                                                </label>
                                                @if($opt->image)
                                                    <span class="text-xs text-gray-400">Auto-saves on select</span>
                                                @endif
                                            </form>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <form method="POST" action="{{ route('admin.grooming.toggle', $opt) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1 rounded-lg text-xs {{ $opt->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }} transition-all">
                                                    {{ $opt->is_active ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.grooming.destroy', $opt) }}"
                                                  onsubmit="return confirm('Remove {{ addslashes($opt->name) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-100 transition-all">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm italic py-3">No styles added yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Add-ons -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <i class="bi bi-plus-circle text-gray-500"></i> Add-on Services
                            </h3>
                            <button onclick="openAddGrooming('addon')"
                                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 rounded-lg text-xs font-semibold text-white transition-all hover:scale-105">
                                <i class="bi bi-plus mr-1"></i> Add Add-on
                            </button>
                        </div>
                        <div class="space-y-3">
                            @forelse($groomingAddons as $opt)
                                <div class="bg-gray-50/40 border {{ $opt->is_active ? 'border-gray-200' : 'border-gray-300/30 opacity-60' }} rounded-xl p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="shrink-0">
                                            @if($opt->image)
                                                <img src="{{ asset('storage/' . $opt->image) }}"
                                                     alt="{{ $opt->name }}"
                                                     class="w-16 h-16 rounded-xl object-cover border border-gray-300">
                                            @else
                                                <div class="w-16 h-16 rounded-xl bg-white border border-gray-200 flex items-center justify-center">
                                                    <i class="bi bi-plus-circle text-gray-400 text-xl"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm">
                                                {{ $opt->name }}
                                                @if(!$opt->is_active)<span class="ml-2 text-xs text-gray-400">(disabled)</span>@endif
                                            </p>
                                            @if($opt->description)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $opt->description }}</p>
                                            @endif

                                            <form method="POST" action="{{ route('admin.grooming.image', $opt) }}"
                                                  enctype="multipart/form-data" class="mt-2 flex items-center gap-2">
                                                @csrf
                                                <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 cursor-pointer text-xs text-indigo-700 transition-all">
                                                    <i class="bi bi-cloud-upload"></i>
                                                    {{ $opt->image ? 'Replace Image' : 'Upload Image' }}
                                                    <input type="file" name="image" accept="image/*" class="hidden"
                                                           onchange="this.closest('form').submit()">
                                                </label>
                                                @if($opt->image)
                                                    <span class="text-xs text-gray-400">Auto-saves on select</span>
                                                @endif
                                            </form>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <form method="POST" action="{{ route('admin.grooming.toggle', $opt) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1 rounded-lg text-xs {{ $opt->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }} transition-all">
                                                    {{ $opt->is_active ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.grooming.destroy', $opt) }}"
                                                  onsubmit="return confirm('Remove {{ addslashes($opt->name) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-100 transition-all">
                                                    <i class="bi bi-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm italic py-3">No add-ons added yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <!-- panel-services closes here, correctly, before booking-services starts -->

                <!-- Booking Services (customer-facing appointment services) -->
                <div id="panel-booking-services" class="tab-panel hidden">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Booking Services</h2>
                            <p class="text-gray-500 text-sm">Services customers can select when requesting an appointment.</p>
                        </div>
                        <button onclick="toggleModal('add-service-modal','add-service-modal-content')"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-sm font-semibold transition-all hover:scale-105 text-white">
                            <i class="bi bi-plus-circle mr-1"></i> Add Service
                        </button>
                    </div>

                    @php
                        $categoryMeta = [
                            'Grooming Packages' => ['icon' => 'bi-scissors',      'bar' => 'bg-emerald-500'],
                            'Add-ons'           => ['icon' => 'bi-droplet',       'bar' => 'bg-teal-500'],
                            'Ala Carte'         => ['icon' => 'bi-check2-circle', 'bar' => 'bg-violet-500'],
                        ];
                    @endphp
                    <div class="space-y-3 mb-8">
                        @foreach(\App\Models\Service::CATEGORIES as $i => $category)
                            @php
                                $items = $bookingServices->get($category, collect());
                                $meta = $categoryMeta[$category] ?? ['icon' => 'bi-tag', 'bar' => 'bg-gray-500'];
                            @endphp
                            <div class="border border-gray-200 rounded-2xl overflow-hidden">
                                <button type="button" onclick="toggleServiceCategory('{{ $i }}')"
                                        class="w-full flex items-center justify-between gap-4 px-5 py-4 {{ $meta['bar'] }} text-white text-left transition-all hover:opacity-95">
                                    <span class="flex items-center gap-3">
                                        <i class="bi {{ $meta['icon'] }}"></i>
                                        <span class="font-bold">{{ $category }}</span>
                                        <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">{{ $items->count() }}</span>
                                    </span>
                                    <i id="svc-chevron-{{ $i }}" class="bi bi-chevron-down transition-transform duration-200 {{ $i === 0 ? 'rotate-180' : '' }}"></i>
                                </button>
                                <div id="svc-panel-{{ $i }}" class="{{ $i === 0 ? '' : 'hidden' }} p-4 bg-white space-y-2">
                                    @forelse($items as $svc)
                                        <div class="bg-gray-50/40 border {{ $svc->is_active ? 'border-gray-200' : 'border-gray-300/30 opacity-60' }} rounded-xl p-4 flex items-center justify-between">
                                            <div>
                                                <p class="font-semibold text-gray-900 text-sm">
                                                    {{ $svc->name }}
                                                    @if(!$svc->is_active)<span class="ml-2 text-xs text-gray-400">(disabled)</span>@endif
                                                </p>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $svc->price_range }}</p>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <button type="button"
                                                        onclick='openEditService({{ $svc->id }}, {{ Illuminate\Support\Js::from($svc->name) }}, {{ Illuminate\Support\Js::from($svc->category) }}, {{ Illuminate\Support\Js::from($svc->prices ?? []) }})'
                                                        class="px-3 py-1 rounded-lg text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <form method="POST" action="{{ route('admin.services.toggle', $svc) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="px-3 py-1 rounded-lg text-xs {{ $svc->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }} transition-all">
                                                        {{ $svc->is_active ? 'Disable' : 'Enable' }}
                                                    </button>
                                                </form>
                                                <form id="delete-service-{{ $svc->id }}" method="POST" action="{{ route('admin.services.destroy', $svc) }}">@csrf @method('DELETE')</form>
                                                <button onclick="confirmDelete('delete-service-{{ $svc->id }}','Archive \'{{ addslashes($svc->name) }}\'? You can restore it later from the Archived list.')"
                                                        class="px-3 py-1 rounded-lg text-xs bg-rose-50 text-rose-600 hover:bg-rose-100 transition-all">
                                                    <i class="bi bi-archive"></i> Archive
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-gray-400 text-sm italic py-2">No services in this category yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Archived Services -->
                    <div class="mt-10 pt-6 border-t border-gray-200">
                        <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <i class="bi bi-archive text-gray-400"></i> Archived Services
                        </h3>
                        <p class="text-gray-400 text-sm mb-4">Hidden from booking. Restore anytime to bring them back.</p>
                        <div class="space-y-2">
                            @forelse($archivedServices as $svc)
                                <div class="bg-gray-50/40 border border-gray-200/60 rounded-xl p-4 flex items-center justify-between opacity-75">
                                    <div>
                                        <p class="font-semibold text-gray-700 text-sm">{{ $svc->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $svc->category }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.services.restore', $svc) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1 rounded-lg text-xs bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-all">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm italic py-2">No archived services.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Settings placeholder (referenced by sidebar button, add real content later) -->
                <div id="panel-settings" class="tab-panel hidden">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">System Settings</h2>
                    <p class="text-gray-500 text-sm italic">Coming soon.</p>
                </div>

            </div>
        </div>
    </main>

    <!-- Add Staff Modal -->
    <div id="add-staff-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/90 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal('add-staff-modal','add-staff-modal-content')">
        <div id="add-staff-modal-content" class="bg-white border border-gray-300 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Add New Staff Member</h2>
            <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
                @csrf
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all" placeholder="e.g. Maria Santos"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all" placeholder="staff@furcare.com"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all" placeholder="Minimum 8 characters"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all" placeholder="Repeat password"></div>
                @if($errors->any())
                    <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                    </div>
                @endif
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleModal('add-staff-modal','add-staff-modal-content')" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition-all hover:scale-[1.02]">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Grooming Option Modal -->
    <div id="add-grooming-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/90 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal('add-grooming-modal','add-grooming-modal-content')">
        <div id="add-grooming-modal-content" class="bg-white border border-gray-300 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out">
            <h2 id="grooming-modal-title" class="text-xl font-bold text-gray-900 mb-6">Add Grooming Option</h2>
            <form method="POST" action="{{ route('admin.grooming.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" id="grooming-type-input" name="type" value="style">
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Name</label>
                    <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all" placeholder="e.g. Teddy Bear Cut"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Description <span class="normal-case font-normal">(optional)</span></label>
                    <input type="text" name="description" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all" placeholder="Brief description..."></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleModal('add-grooming-modal','add-grooming-modal-content')" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition-all hover:scale-[1.02]">Add Option</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div id="add-service-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/90 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal('add-service-modal','add-service-modal-content')">
        <div id="add-service-modal-content" class="bg-white border border-gray-300 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[85vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Add New Service</h2>
            <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-4">
                @csrf
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Service Name</label>
                    <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all" placeholder="e.g. Spa Treatment"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Category</label>
                    <select name="category" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all">
                        @foreach(\App\Models\Service::CATEGORIES as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select></div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Pricing</label>
                    <div class="flex gap-2 mb-3 bg-gray-100 p-1 rounded-xl">
                        <button type="button" onclick="setAddPricingMode('size')" id="add-mode-btn-size" class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold transition-all bg-white shadow text-gray-900">By Pet Size</button>
                        <button type="button" onclick="setAddPricingMode('flat')" id="add-mode-btn-flat" class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold transition-all text-gray-500">Flat Rate</button>
                    </div>
                    <input type="hidden" name="pricing_mode" id="add-pricing-mode" value="size">

                    <div id="add-size-prices" class="grid grid-cols-3 gap-2">
                        @foreach(\App\Models\Service::SIZES as $size)
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 mb-1">{{ $size }}</label>
                                <input type="number" step="0.01" min="0" name="prices[{{ $size }}]" placeholder="0.00"
                                       class="w-full bg-gray-50 border border-gray-300 rounded-lg px-2 py-2 text-sm outline-none focus:border-indigo-400 transition-all">
                            </div>
                        @endforeach
                    </div>
                    <div id="add-flat-price" class="hidden">
                        <input type="number" step="0.01" min="0" name="flat_price" placeholder="0.00"
                               class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-sm outline-none focus:border-indigo-400 transition-all">
                        <p class="text-gray-400 text-xs mt-1">One price regardless of pet size (e.g. per-pack treatments).</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleModal('add-service-modal','add-service-modal-content')" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition-all hover:scale-[1.02]">Add Service</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div id="edit-service-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/90 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal('edit-service-modal','edit-service-modal-content')">
        <div id="edit-service-modal-content" class="bg-white border border-gray-300 rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[85vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Edit Service</h2>
            <form id="edit-service-form" method="POST" class="space-y-4">
                @csrf @method('PATCH')
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Service Name</label>
                    <input type="text" id="edit-service-name" name="name" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all"></div>
                <div><label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Category</label>
                    <select id="edit-service-category" name="category" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-gray-900 outline-none focus:border-indigo-400 transition-all">
                        @foreach(\App\Models\Service::CATEGORIES as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select></div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Pricing</label>
                    <div class="flex gap-2 mb-3 bg-gray-100 p-1 rounded-xl">
                        <button type="button" onclick="setEditPricingMode('size')" id="edit-mode-btn-size" class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold transition-all bg-white shadow text-gray-900">By Pet Size</button>
                        <button type="button" onclick="setEditPricingMode('flat')" id="edit-mode-btn-flat" class="flex-1 px-3 py-2 rounded-lg text-xs font-semibold transition-all text-gray-500">Flat Rate</button>
                    </div>
                    <input type="hidden" name="pricing_mode" id="edit-pricing-mode" value="size">

                    <div id="edit-size-prices" class="grid grid-cols-3 gap-2">
                        @foreach(\App\Models\Service::SIZES as $size)
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 mb-1">{{ $size }}</label>
                                <input type="number" step="0.01" min="0" name="prices[{{ $size }}]" id="edit-price-{{ $size }}" placeholder="0.00"
                                       class="w-full bg-gray-50 border border-gray-300 rounded-lg px-2 py-2 text-sm outline-none focus:border-indigo-400 transition-all">
                            </div>
                        @endforeach
                    </div>
                    <div id="edit-flat-price" class="hidden">
                        <input type="number" step="0.01" min="0" name="flat_price" id="edit-price-flat" placeholder="0.00"
                               class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-3 text-sm outline-none focus:border-indigo-400 transition-all">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleModal('edit-service-modal','edit-service-modal-content')" class="flex-1 px-4 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold transition-all hover:scale-[1.02]">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>