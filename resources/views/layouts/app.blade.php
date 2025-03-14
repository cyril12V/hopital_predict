<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- ✅ Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- ✅ Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- ✅ Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">
    <div class="flex h-screen" x-data="{ open: false }">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <div class="flex flex-col flex-1">
       

            <!-- Contenu -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    
</body>
</html>
