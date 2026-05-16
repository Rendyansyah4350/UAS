<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Eduvan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden relative">

        <aside id="sidebarAdmin"
            class="w-64 bg-slate-800 text-white flex-shrink-0 hidden md:flex flex-col fixed inset-y-0 left-0 z-50 md:relative transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-4 flex justify-end md:hidden">
                <button onclick="toggleSidebarAdmin()" class="text-white focus:outline-none text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 text-2xl font-bold border-b border-slate-700">
                Eduvan Admin
            </div>
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                    class="block p-3 rounded transition {{ request()->is('admin/dashboard') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>

                <a href="{{ route('admin.courses.index') }}"
                    class="block p-3 rounded transition {{ request()->is('admin/courses*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-book mr-2"></i> Kelola Course
                </a>

                <a href="{{ route('admin.students.index') }}"
                    class="block p-3 rounded transition {{ request()->routeIs('admin.students.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-users mr-2"></i>
                    <span>Student</span>
                </a>

                <a href="{{ route('admin.pembelian.index') }}"
                    class="block p-3 rounded transition {{ request()->is('admin/pembelian*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-shopping-cart mr-2"></i> Pembelian
                </a>

                <a href="{{ route('admin.quiz.index') }}"
                    class="block p-3 rounded transition {{ request()->is('admin/quiz*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-tasks mr-2"></i> Quiz & Progress
                </a>

                <a href="{{ route('admin.certificates.index') }}"
                    class="block p-3 rounded transition {{ request()->routeIs('admin.certificates.*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-award mr-2"></i> Certificate
                </a>
            </nav>
        </aside>

        <div id="sidebarBackdrop" onclick="toggleSidebarAdmin()"
            class="hidden fixed inset-0 bg-black opacity-50 z-40 md:hidden"></div>

        <main class="flex-1 overflow-y-auto w-full">
            <header class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-30">
                <div class="flex items-center space-x-3">
                    <button onclick="toggleSidebarAdmin()"
                        class="text-gray-600 focus:outline-none md:hidden text-xl cursor-pointer">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="text-base md:text-xl font-semibold text-gray-800 truncate">Dashboard Monitoring</h2>
                </div>
                <div class="flex items-center space-x-2 md:space-x-4">
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline m-0 p-0">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 md:px-4 md:py-2 rounded text-xs md:text-sm font-medium transition cursor-pointer flex items-center gap-1">
                            <i class="fas fa-sign-out-alt"></i> <span class="hidden xs:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <div class="p-4 md:p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleSidebarAdmin() {
            const sidebar = document.getElementById('sidebarAdmin');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (sidebar.classList.contains('hidden')) {
                // Tampilkan sidebar di mobile
                sidebar.classList.remove('hidden');
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('flex', 'translate-x-0');
                backdrop.classList.remove('hidden');
            } else {
                // Sembunyikan sidebar di mobile
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                backdrop.classList.add('hidden');
                // Kasih delay dikit biar animasi transisinya smooth pas nutup
                setTimeout(() => {
                    if (window.innerWidth < 768) {
                        sidebar.classList.add('hidden');
                    }
                }, 300);
            }
        }

        // Jaga-jaga kalau user resize browser dari mode HP ke Desktop
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
