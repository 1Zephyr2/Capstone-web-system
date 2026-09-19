<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | My Profile</title>
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
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen">

    <nav class="w-full bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-lg font-bold flex items-center gap-2 text-emerald-700">
                <img src="{{ asset('paw-icon.png') }}" class="w-7 h-7" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full text-sm border border-gray-200 hover:bg-gray-50 text-gray-600 transition-all">← Dashboard</a>
                @include('components.notification-bell', ['notifRoutePrefix' => ''])
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf <button class="px-4 py-2 rounded-full text-sm bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 transition-all">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 sm:px-6 py-8 max-w-2xl">

        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">My Profile</h1>
            <p class="text-gray-400 text-sm">Manage your account information and password.</p>
        </header>

        @if(session('status') === 'profile-updated')
            <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill shrink-0"></i> Profile updated successfully.
            </div>
        @endif
        @if(session('status') === 'password-updated')
            <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill shrink-0"></i> Password updated successfully.
            </div>
        @endif

        <div class="space-y-5">

            <!-- Profile Info -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-1">Profile Information</h2>
                <p class="text-gray-400 text-sm mb-5">Update your name and email address.</p>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf @method('patch')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">First Name</label>
                            <input type="text" name="first_name"
                                   value="{{ old('first_name', explode(' ', $user->name)[0] ?? '') }}"
                                   required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-100 transition-all text-sm" placeholder="e.g. John">
                            @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Last Name</label>
                            <input type="text" name="last_name"
                                   value="{{ old('last_name', implode(' ', array_slice(explode(' ', $user->name), 1)) ?: '') }}"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-100 transition-all text-sm" placeholder="e.g. Doe">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-100 transition-all text-sm">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Mobile Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required placeholder="e.g. 0917 123 4567"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-100 transition-all text-sm">
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all text-sm">Save Changes</button>
                </form>
            </div>

            <!-- Password -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-1">Update Password</h2>
                <p class="text-gray-400 text-sm mb-5">Use a long, random password to keep your account secure.</p>
                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf @method('put')
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Current Password</label>
                        <input type="password" name="current_password" autocomplete="current-password"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all text-sm">
                        @error('current_password', 'updatePassword')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">New Password</label>
                        <input type="password" name="password" autocomplete="new-password"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all text-sm">
                        @error('password', 'updatePassword')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-emerald-500 transition-all text-sm">
                    </div>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold transition-all text-sm">Update Password</button>
                </form>
            </div>

            <!-- Delete Account -->
            <div class="bg-white border border-red-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-1">Delete Account</h2>
                <p class="text-gray-400 text-sm mb-5">Once deleted, all data will be permanently removed.</p>
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure? This cannot be undone.')">
                    @csrf @method('delete')
                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Confirm Password</label>
                        <input type="password" name="password"
                               class="w-full bg-gray-50 border border-red-200 rounded-xl px-4 py-2.5 text-gray-900 outline-none focus:border-red-400 transition-all text-sm" placeholder="Enter password to confirm">
                        @error('password', 'userDeletion')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-50 border border-red-200 text-red-500 hover:bg-red-100 font-semibold transition-all text-sm">
                        <i class="bi bi-trash mr-1"></i>Delete My Account
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>