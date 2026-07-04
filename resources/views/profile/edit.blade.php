<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FURCARE | My Profile</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('furcare.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-slate-950 text-slate-200 antialiased min-h-screen">

    <nav class="relative z-50 w-full bg-[#0b0f19] backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight flex items-center gap-2 text-white">
                <img src="{{ asset('paw-icon.png') }}" class="w-8 h-8" alt="Logo"> FURCARE
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full text-sm bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all">← Dashboard</a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-full text-sm bg-red-900/30 hover:bg-red-900/50 text-red-400 transition-all">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12 max-w-2xl">

        <header class="mb-8">
            <h1 class="text-2xl font-bold text-white mb-1">My Profile</h1>
            <p class="text-slate-400 text-sm">Manage your account information and password.</p>
        </header>

        @if(session('status') === 'profile-updated')
            <div class="mb-6 px-5 py-3 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill"></i> Profile updated successfully.
            </div>
        @endif
        @if(session('status') === 'password-updated')
            <div class="mb-6 px-5 py-3 rounded-xl bg-teal-500/10 border border-teal-500/30 text-teal-300 flex items-center gap-3 text-sm">
                <i class="bi bi-check-circle-fill"></i> Password updated successfully.
            </div>
        @endif

        <div class="space-y-6">

            <!-- ── Profile Information ──────────────────────────────── -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-8">
                <h2 class="text-lg font-bold text-white mb-1">Profile Information</h2>
                <p class="text-slate-400 text-sm mb-6">Update your name and email address.</p>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf @method('patch')

                    <!-- First + Last Name -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">First Name</label>
                            <input type="text" name="first_name"
                                   value="{{ old('first_name', explode(' ', $user->name)[0] ?? '') }}"
                                   required
                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all text-sm"
                                   placeholder="e.g. John">
                            @error('first_name')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Last Name</label>
                            <input type="text" name="last_name"
                                   value="{{ old('last_name', implode(' ', array_slice(explode(' ', $user->name), 1)) ?: '') }}"
                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all text-sm"
                                   placeholder="e.g. Doe">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Email Address</label>
                        <input type="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all text-sm">
                        @error('email')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2 p-3 rounded-lg bg-amber-500/10 border border-amber-500/20">
                                <p class="text-amber-300 text-xs">Your email is unverified.
                                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="inline">@csrf</form>
                                    <button form="send-verification" class="underline text-amber-400 hover:text-amber-300">Resend verification email.</button>
                                </p>
                            </div>
                        @endif
                    </div>

                    <button type="submit"
                            class="px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-[1.02] text-sm">
                        Save Changes
                    </button>
                </form>
            </div>

            <!-- ── Update Password ─────────────────────────────────── -->
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-2xl p-8">
                <h2 class="text-lg font-bold text-white mb-1">Update Password</h2>
                <p class="text-slate-400 text-sm mb-6">Use a long, random password to keep your account secure.</p>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf @method('put')

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Current Password</label>
                        <input type="password" name="current_password" autocomplete="current-password"
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all text-sm">
                        @error('current_password', 'updatePassword')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">New Password</label>
                        <input type="password" name="password" autocomplete="new-password"
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all text-sm">
                        @error('password', 'updatePassword')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none focus:border-teal-500 transition-all text-sm">
                    </div>

                    <button type="submit"
                            class="px-6 py-3 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-semibold transition-all hover:scale-[1.02] text-sm">
                        Update Password
                    </button>
                </form>
            </div>

            <!-- ── Delete Account ──────────────────────────────────── -->
            <div class="bg-slate-900/40 border border-red-500/20 rounded-2xl p-8">
                <h2 class="text-lg font-bold text-white mb-1">Delete Account</h2>
                <p class="text-slate-400 text-sm mb-6">Once deleted, all your data will be permanently removed.</p>

                <form method="POST" action="{{ route('profile.destroy') }}"
                      onsubmit="return confirm('Are you sure? This cannot be undone.')">
                    @csrf @method('delete')
                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Confirm Password</label>
                        <input type="password" name="password"
                               class="w-full bg-slate-950 border border-red-500/30 rounded-xl px-4 py-3 text-white outline-none focus:border-red-500 transition-all text-sm"
                               placeholder="Enter your password to confirm">
                        @error('password', 'userDeletion')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                            class="px-6 py-3 rounded-xl bg-red-900/40 hover:bg-red-900/60 border border-red-500/30 text-red-400 font-semibold transition-all text-sm">
                        <i class="bi bi-trash mr-2"></i>Delete My Account
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>