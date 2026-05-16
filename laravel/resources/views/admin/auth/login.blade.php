<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - EduVan</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-slate-900 text-slate-100 flex min-h-screen items-center justify-center p-4">

    <div class="w-full max-w-md bg-slate-800 rounded-2xl shadow-xl border border-slate-700 p-8">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-indigo-400">EduVan</h1>
            <p class="text-slate-400 text-sm mt-2">Administrator Access Only</p>
        </div>

        @if (session('error'))
            <div class="bg-red-500/10 border border-red-500/50 text-red-400 text-sm p-3 rounded-lg mb-4 text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('admin/login') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500 @error('email') border-red-500 @enderror"
                    placeholder="admin@gmal.com">

                @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                        class="w-full bg-slate-950 border border-slate-700 rounded-lg pl-4 pr-12 py-2.5 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-indigo-500"
                        placeholder="••••••••">

                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-500 hover:text-slate-300 cursor-pointer">
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg id="eyeClose" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg py-2.5 transition duration-200 shadow-lg shadow-indigo-600/20 cursor-pointer">
                Sign In
            </button>
        </form>

    </div>

</body>
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');
    const eyeOpen = document.querySelector('#eyeOpen');
    const eyeClose = document.querySelector('#eyeClose');

    togglePassword.addEventListener('click', function() {
        // Cek tipe input saat ini
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Tukar tampilan icon mata
        if (type === 'password') {
            eyeOpen.classList.remove('hidden');
            eyeClose.classList.add('hidden');
        } else {
            eyeOpen.classList.add('hidden');
            eyeClose.classList.remove('hidden');
        }
    });
</script>

</html>
