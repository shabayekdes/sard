<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sard')</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900">
    <header class="bg-white border-b">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-lg font-bold">Sard</a>
            <nav>
                <a href="/" class="text-sm text-gray-600 mr-4">Home</a>
                <a href="/ai/summarize-test" class="text-sm text-gray-600">AI Test</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="container mx-auto px-4 py-6 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} Sard
    </footer>
</body>
</html>

