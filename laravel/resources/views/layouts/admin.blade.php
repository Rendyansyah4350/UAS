<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Eduvan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Custom scrollbar biar estetik */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            bg: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>

<body class="bg-gray-100 antialiased overflow-x-hidden relative">
    <div class="flex h-screen overflow-hidden">

        <aside id="sidebarAdmin"
            class="w-64 bg-slate-800 text-white flex-shrink-0 hidden md:flex flex-col fixed inset-y-0 left-0 z-50 md:relative transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl shadow-slate-900/30 border-r border-slate-700/50">

            <div class="p-4 flex justify-end md:hidden border-b border-slate-700">
                <button onclick="toggleSidebarAdmin()"
                    class="w-9 h-9 flex items-center justify-center bg-slate-700/50 hover:bg-slate-700 text-white rounded-xl transition focus:outline-none">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="p-6 border-b border-slate-700 flex flex-col items-center text-center gap-4 bg-slate-900/20">

                <div
                    class="w-28 h-28 flex items-center justify-center transition-transform duration-300 hover:scale-105">
                    <img src="{{ asset('assets/images/eduvan.png') }}" alt="Logo EduVan"
                        class="w-full h-full object-contain">
                </div>

                <div class="space-y-0.5">
                    <h1 class="text-2xl font-black text-white tracking-tighter">Eduvan Admin</h1>
                    <p class="text-[10px] text-slate-400 font-medium tracking-wide uppercase leading-none">Powered by
                        EduVan Team</p>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2.5 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-3 p-3 rounded-xl transition {{ request()->is('admin/dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i
                        class="fas fa-home text-sm {{ request()->is('admin/dashboard') ? '' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span class="font-semibold text-sm">Dashboard</span>
                </a>

                <a href="{{ route('admin.courses.index') }}"
                    class="group flex items-center gap-3 p-3 rounded-xl transition {{ request()->is('admin/courses*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i
                        class="fas fa-book text-sm {{ request()->is('admin/courses*') ? '' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span class="font-semibold text-sm">Kelola Course</span>
                </a>

                <a href="{{ route('admin.students.index') }}"
                    class="group flex items-center gap-3 p-3 rounded-xl transition {{ request()->routeIs('admin.students.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i
                        class="fas fa-users text-sm {{ request()->routeIs('admin.students.*') ? '' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span class="font-semibold text-sm">Student</span>
                </a>

                <a href="{{ route('admin.pembelian.index') }}"
                    class="group flex items-center gap-3 p-3 rounded-xl transition {{ request()->is('admin/pembelian*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i
                        class="fas fa-shopping-cart text-sm {{ request()->is('admin/pembelian*') ? '' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span class="font-semibold text-sm">Laporan Pembelian</span>
                </a>

                <a href="{{ route('admin.quiz.index') }}"
                    class="group flex items-center gap-3 p-3 rounded-xl transition {{ request()->is('admin/quiz*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i
                        class="fas fa-tasks text-sm {{ request()->is('admin/quiz*') ? '' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span class="font-semibold text-sm">Quiz & Progress</span>
                </a>

                <a href="{{ route('admin.certificates.index') }}"
                    class="group flex items-center gap-3 p-3 rounded-xl transition {{ request()->routeIs('admin.certificates.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <i
                        class="fas fa-award text-sm {{ request()->routeIs('admin.certificates.*') ? '' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                    <span class="font-semibold text-sm">Certificate</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-700 bg-slate-900/20">
                <button type="button" onclick="openLogoutModal()"
                    class="w-full flex items-center justify-center gap-2.5 p-3 rounded-xl bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white text-xs font-black shadow-lg shadow-rose-600/10 active:scale-98 transition cursor-pointer">
                    <i class="fas fa-sign-out-alt text-xs"></i> Log Out
                </button>
            </div>
        </aside>

        <div id="sidebarBackdrop" onclick="toggleSidebarAdmin()"
            class="hidden fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm transition-opacity duration-300">
        </div>

        <main class="flex-1 overflow-y-auto w-full">
            <header
                class="bg-white shadow-sm p-4 px-6 flex justify-between items-center sticky top-0 z-30 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <button onclick="toggleSidebarAdmin()"
                        class="w-9 h-9 flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-600 focus:outline-none md:hidden rounded-xl text-lg transition focus:ring-2 focus:ring-indigo-500/20">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="text-base md:text-xl font-black text-gray-900 tracking-tight truncate">Dashboard
                        Monitoring</h2>
                </div>
                <div class="flex items-center space-x-2 md:space-x-4">
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col text-right">
                            <span class="text-sm font-black text-gray-800">Admin EduVan</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-4 md:p-6 lg:p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <div id="logoutModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm transition-all duration-300 hidden opacity-0">
        <div class="fixed inset-0 bg-transparent" onclick="closeLogoutModal()"></div>

        <div
            class="relative bg-slate-900 w-full max-w-sm rounded-2xl shadow-2xl border border-slate-800 p-6 transform transition-all duration-300 scale-95 opacity-0 z-10 text-center">

            <div
                class="w-14 h-14 bg-red-500/10 border border-red-500/20 text-red-400 rounded-full flex items-center justify-center text-xl mx-auto mb-4 shadow-lg shadow-red-500/5">
                <i class="fas fa-exclamation-triangle"></i>
            </div>

            <h3 class="text-lg font-bold text-slate-100 tracking-tight">Konfirmasi Keluar</h3>
            <p class="text-sm text-slate-400 mt-2 leading-relaxed">Apakah Anda yakin ingin mengakhiri sesi administrator
                dan keluar dari sistem EduVan?</p>

            <div class="grid grid-cols-2 gap-3 mt-6">
                <button type="button" onclick="closeLogoutModal()"
                    class="w-full py-2.5 rounded-xl border border-slate-700 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold transition active:scale-98 cursor-pointer">
                    Batal
                </button>

                <form action="{{ route('admin.logout') }}" method="POST" class="m-0 p-0 inline">
                    @csrf
                    <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white text-sm font-bold shadow-lg shadow-red-600/10 transition active:scale-98 cursor-pointer">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            const modal = document.getElementById('logoutModal');
            const content = modal.querySelector('.transform');

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 20);
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            const content = modal.querySelector('.transform');

            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function toggleSidebarAdmin() {
            const sidebar = document.getElementById('sidebarAdmin');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('flex', 'translate-x-0');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                backdrop.classList.add('hidden');
                setTimeout(() => {
                    if (window.innerWidth < 768) {
                        sidebar.classList.add('hidden');
                    }
                }, 300);
            }
        }

        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebarAdmin');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('hidden', '-translate-x-full');
                sidebar.classList.add('flex', 'translate-x-0');
                backdrop.classList.add('hidden');
            } else {
                if (!sidebar.classList.contains('translate-x-0')) {
                    sidebar.classList.add('hidden');
                }
            }
        });
    </script>
</body>

</html>
