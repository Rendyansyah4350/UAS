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
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-800 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-slate-700">
                Eduvan Admin
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="block p-3 rounded transition {{ request()->is('admin/dashboard') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>

                <!-- Kelola Course -->
                <a href="{{ route('admin.courses.index') }}"
                    class="block p-3 rounded transition {{ request()->is('admin/courses*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-book mr-2"></i> Kelola Course
                </a>

                <!-- Student -->
                <a href="{{ route('admin.students.index') }}" 
                class="block p-3 rounded transition {{ request()->routeIs('admin.students.*') ? 'bg-blue-600 text-white' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-users mr-2"></i>
                    <span>Student</span>
                </a>

                <!-- Pembelian -->
                <a href="{{ route('admin.pembelian.index') }}"
                    class="block p-3 rounded transition {{ request()->is('admin/pembelian*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-shopping-cart mr-2"></i> Pembelian
                </a>

                <!-- Quiz & Progress -->
                <a href="#"
                    class="block p-3 rounded transition {{ request()->is('admin/quiz*') ? 'bg-blue-600' : 'hover:bg-slate-700' }}">
                    <i class="fas fa-tasks mr-2"></i> Quiz & Progress
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Navbar -->
            <header class="bg-white shadow-sm p-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Dashboard Monitoring</h2>
                <div class="flex items-center space-y-2">
                    <span class="mr-4 text-gray-600">Admin Mode</span>
                    <button class="bg-red-500 text-white px-4 py-2 rounded text-sm">Logout</button>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
