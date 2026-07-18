<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | Dashboard</title>
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
        function toggleNav() { document.getElementById('mobile-menu').classList.toggle('hidden'); }
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
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased relative overflow-x-hidden min-h-screen flex flex-col">

    <nav class="relative w-full z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-lg sm:text-xl font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-7 h-7 sm:w-8 sm:h-8" alt="Logo"> FURCARE
            </a>
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-full text-sm border border-gray-200 hover:bg-gray-50 text-gray-600 transition-all">
                    <i class="bi bi-calendar-check mr-1 text-emerald-600"></i> My Appointments
                </a>
                <a href="{{ route('request.appointment') }}" class="px-4 py-2 rounded-full text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all">
                    <i class="bi bi-plus-circle mr-1"></i> Request Appointment
                </a>
                <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-full border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-all">
                    <i class="bi bi-person"></i>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf <button class="px-4 py-2 rounded-full text-sm bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 transition-all">Logout</button>
                </form>
            </div>
            <button onclick="toggleNav()" class="md:hidden w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </nav>
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
        <a href="{{ route('appointments.index') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-emerald-50 text-gray-600 text-sm"><i class="bi bi-calendar-check text-emerald-600"></i> My Appointments</a>
        <a href="{{ route('request.appointment') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-50 text-emerald-700 text-sm font-semibold"><i class="bi bi-plus-circle"></i> Request Appointment</a>
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg hover:bg-gray-50 text-gray-600 text-sm"><i class="bi bi-person"></i> Profile</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf <button class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-500 text-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
        </form>
    </div>

    <main class="flex-grow container mx-auto px-4 sm:px-6 py-8 sm:py-12 relative z-10">

        @if(session('success'))
            <div class="mb-6 px-4 sm:px-6 py-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill shrink-0"></i> {{ session('success') }}
            </div>
        @endif

        <header class="mb-8 sm:mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-500 text-sm">Here's what's happening with your pets.</p>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                <svg class="inline w-[1em] h-[1em] text-violet-600 text-xl mb-2 block" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg>
                <h5 class="text-gray-500 text-xs mb-1">Your Pets</h5>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_pets'] }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                <i class="bi bi-calendar-check text-emerald-600 text-xl mb-2 block"></i>
                <h5 class="text-gray-500 text-xs mb-1">Upcoming Appointments</h5>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming'] }}</p>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                <i class="bi bi-hourglass-split text-amber-600 text-xl mb-2 block"></i>
                <h5 class="text-gray-500 text-xs mb-1">Awaiting Approval</h5>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- My Pets -->
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">My Pets</h3>
                    <button onclick="toggleModal('add-pet-modal','add-pet-modal-content')"
                            class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-all">
                        <i class="bi bi-plus-circle mr-1"></i> Add Pet
                    </button>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    @forelse($pets as $pet)
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-emerald-300 transition-all">
                            <div class="flex items-center gap-3 mb-3">
                                @if($pet->photo_url)
                                    <img src="{{ $pet->photo_url }}" alt="{{ $pet->name }}" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                                        <svg class="inline w-[1em] h-[1em]" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 truncate">{{ $pet->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $pet->breed }} &bull; {{ $pet->age }} {{ Str::plural('yr', $pet->age) }} old</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pets.details', $pet->id) }}" class="flex-1 text-center px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 text-xs font-semibold transition-all">View</a>
                                <a href="{{ route('pets.edit', $pet->id) }}" class="flex-1 text-center px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 text-xs font-semibold transition-all">Edit</a>
                                <form id="delete-pet-{{ $pet->id }}" method="POST" action="{{ route('pets.destroy', $pet) }}">@csrf @method('DELETE')</form>
                                <button type="button" onclick="confirmDelete('delete-pet-{{ $pet->id }}','Remove {{ addslashes($pet->name) }}?')"
                                        class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 transition-all"><i class="bi bi-trash text-xs"></i></button>
                            </div>
                        </div>
                    @empty
                        <div class="sm:col-span-2 text-center py-10">
                            <svg class="inline w-[1em] h-[1em] text-4xl text-gray-300 mb-3 block" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><ellipse cx="4.5" cy="6.5" rx="1.3" ry="1.7"/><ellipse cx="8" cy="4.5" rx="1.3" ry="1.7"/><ellipse cx="11.5" cy="6.5" rx="1.3" ry="1.7"/><path d="M8 7.2c-2.1 0-3.9 1.7-3.9 3.5 0 1.1.9 1.7 2 1.7.6 0 1.1-.2 1.9-.2s1.3.2 1.9.2c1.1 0 2-.6 2-1.7 0-1.8-1.8-3.5-3.9-3.5z"/></svg>
                            <p class="text-gray-400 text-sm italic">No pets added yet. Click "Add Pet" to get started.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-5">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 text-sm">Upcoming Appointments</h3>
                        <a href="{{ route('appointments.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 transition-all">View all →</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($appointments as $appt)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 mt-0.5">
                                    <i class="bi bi-calendar-check text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $appt->pet->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $appt->service_label }}</p>
                                    <p class="text-xs text-emerald-600">{{ $appt->appointment_date->format('M d — g:i A') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm italic">No upcoming appointments.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out shadow-sm">
                    <h3 class="font-bold text-gray-900 mb-4 text-sm">Quick Actions</h3>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('request.appointment') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-all hover:translate-x-1 text-sm text-emerald-700">
                            <i class="bi bi-plus-circle"></i> Request Appointment
                        </a>
                        <a href="{{ route('appointments.history') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-all hover:translate-x-1 text-sm text-gray-600">
                            <i class="bi bi-clock-history"></i> Appointment History
                        </a>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-all hover:translate-x-1 text-sm text-gray-600">
                            <i class="bi bi-person"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @php
            $dashboardStyles = App\Models\GroomingOption::where('type', 'style')->where('is_active', true)->orderBy('name')->take(4)->get();
        @endphp
        @if($dashboardStyles->count() > 0)
        <div class="mt-10 bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Grooming Styles</h3>
                    <p class="text-gray-400 text-sm">See what your pet could look like.</p>
                </div>
                <a href="{{ route('services') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold transition-all">See all →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($dashboardStyles as $style)
                    <div class="rounded-xl overflow-hidden border border-gray-200">
                        @if($style->image)
                            <img src="{{ asset('storage/' . $style->image) }}" alt="{{ $style->name }}" class="w-full h-24 object-cover">
                        @else
                            <div class="w-full h-24 bg-emerald-50 flex items-center justify-center">
                                <i class="bi bi-scissors text-emerald-400 text-xl"></i>
                            </div>
                        @endif
                        <p class="text-xs font-semibold text-gray-900 p-2 truncate">{{ $style->name }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-6 bg-emerald-50 border border-emerald-100 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 reveal-on-scroll opacity-0 translate-y-10 transition-all duration-700 ease-out">
            <div>
                <h3 class="font-bold text-gray-900 text-sm mb-1">Need to reach Bark Park directly?</h3>
                <p class="text-gray-500 text-xs">We're happy to help with anything not covered here.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="tel:+639700577320" class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-all flex items-center gap-2">
                    <i class="bi bi-telephone text-emerald-600"></i> 0970 057 7320
                </a>
                {{-- TODO: replace with the real Bark Park Facebook page URL --}}
                <a href="https://www.facebook.com/profile.php?id=61564144455710&rdid=X2RMaCnl9m4vtFp7&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1H3MLMSw7o" target="_blank" rel="noopener" class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-all flex items-center gap-2">
                    <i class="bi bi-facebook text-emerald-600"></i> Facebook
                </a>
            </div>
        </div>
    </main>

    <footer class="relative z-10 py-8 text-center border-t border-gray-200 text-gray-400 text-sm">
        &copy; {{ date('Y') }} FURCARE | Pet Care Appointment System
    </footer>

    <!-- Add Pet Modal -->
    <div id="add-pet-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-6 bg-gray-50/90 backdrop-blur-sm transition-opacity duration-300 ease-out"
         onclick="if(event.target===this) toggleModal('add-pet-modal','add-pet-modal-content')">
        <div id="add-pet-modal-content" class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 w-full max-w-md shadow-2xl transform scale-95 opacity-0 transition-all duration-300 ease-out max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Add a Pet</h2>
            <form method="POST" action="{{ route('pets.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Pet Name</label>
                    <input type="text" name="name" required maxlength="100" value="{{ old('name') }}"
                           class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition-all" placeholder="e.g. Max">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Type</label>
                        <select name="type" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition-all">
                            <option value="dog">Dog</option>
                            <option value="cat">Cat</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Age</label>
                        <input type="number" name="age" required min="0" max="100" value="{{ old('age') }}"
                               class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Breed</label>
                    <input type="text" name="breed" required maxlength="100" value="{{ old('breed') }}"
                           class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition-all" placeholder="e.g. Golden Retriever">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Special Notes <span class="normal-case font-normal text-gray-300">(optional)</span></label>
                    <textarea name="special_notes" rows="2" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-emerald-500 transition-all resize-none">{{ old('special_notes') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Photo <span class="normal-case font-normal text-gray-300">(optional)</span></label>
                    <label for="pet-photo-input"
                           class="flex items-center gap-3 w-full bg-gray-50 border border-dashed border-gray-300 rounded-xl px-4 py-3 text-sm cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/40 transition-all">
                        <span class="w-9 h-9 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-emerald-600 shrink-0">
                            <i class="bi bi-cloud-upload"></i>
                        </span>
                        <span class="min-w-0">
                            <span id="pet-photo-filename" class="block text-gray-600 truncate">Click to upload a photo</span>
                            <span class="block text-gray-400 text-xs">PNG, JPG, or WEBP — up to 2MB</span>
                        </span>
                    </label>
                    <input type="file" name="photo" id="pet-photo-input" accept="image/*" class="hidden"
                           onchange="document.getElementById('pet-photo-filename').innerText = this.files.length ? this.files[0].name : 'Click to upload a photo'">
                </div>
                @if($errors->any())
                    <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                    </div>
                @endif
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleModal('add-pet-modal','add-pet-modal-content')" class="flex-1 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition-all">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all">Add Pet</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
