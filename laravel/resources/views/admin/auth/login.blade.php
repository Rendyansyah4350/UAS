<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - EduVan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body
    class="bg-slate-950 text-slate-100 flex min-h-screen items-center justify-center p-4 sm:p-6 relative overflow-hidden">

    <div
        class="absolute top-[-10%] left-[-10%] w-[300px] sm:w-[500px] h-[300px] sm:h-[500px] bg-indigo-600/10 rounded-full blur-[80px] sm:blur-[120px] pointer-events-none">
    </div>
    <div
        class="absolute bottom-[-10%] right-[-10%] w-[300px] sm:w-[500px] h-[300px] sm:h-[500px] bg-blue-600/10 rounded-full blur-[80px] sm:blur-[120px] pointer-events-none">
    </div>

    <div
        class="w-full max-w-md bg-slate-900/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-800 p-6 sm:p-8 relative z-10">

        <div class="text-center mb-6 sm:mb-8 flex flex-col items-center">
            <div
                class="w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center mb-3 sm:mb-4 transition-transform duration-300 hover:scale-105">
                <img src="{{ asset('assets/images/eduvan.png') }}" alt="Logo EduVan"
                    class="w-full h-full object-contain">
            </div>

            <h1
                class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 to-blue-400 bg-clip-text text-transparent">
                EduVan</h1>
            <p class="text-slate-400 text-[10px] sm:text-xs font-semibold tracking-wider uppercase mt-1.5 sm:mt-2">
                Administrator Access Only</p>
        </div>

        @if (session('error'))
            <div
                class="bg-red-500/10 border border-red-500/30 text-red-400 text-xs sm:text-sm p-3 rounded-xl mb-5 sm:mb-6 text-center shadow-inner">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('admin/login') }}" method="POST" class="space-y-4 sm:space-y-5">
            @csrf

            <div>
                <label for="email"
                    class="block text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 sm:mb-2">Email
                    Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 sm:px-4 py-2.5 sm:py-3 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all duration-200 @error('email') border-red-500/50 focus:border-red-500 @enderror"
                    placeholder="admin@gmail.com">

                @error('email')
                    <p class="text-red-400 text-xs mt-1.5 pl-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password"
                    class="block text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 sm:mb-2">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-3.5 sm:pl-4 pr-11 sm:pr-12 py-2.5 sm:py-3 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all duration-200"
                        placeholder="••••••••">

                    <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 sm:pr-4 text-slate-500 hover:text-slate-300 cursor-pointer transition-colors">
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        <svg id="eyeClose" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl py-2.5 sm:py-3 mt-1 sm:mt-2 transition-all duration-300 shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 active:scale-[0.99] cursor-pointer">
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
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

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
