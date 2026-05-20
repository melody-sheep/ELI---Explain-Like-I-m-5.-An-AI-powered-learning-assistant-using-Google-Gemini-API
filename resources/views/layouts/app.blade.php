 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELI - Explain Like I'm 5</title>
    @vite(['resources/css/app.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gradient-to-br from-blue-50 to-purple-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <header class="text-center mb-8">
            <h1 class="text-5xl font-bold text-orange-600 mb-2">🧸 ELI</h1>
            <p class="text-gray-600 text-lg">Explain Like I'm 5 - Simple answers to complex questions</p>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="text-center mt-12 text-gray-500 text-sm">
            <p>Powered by Google Gemini AI | ELI - Making learning simple</p>
        </footer>
    </div>
</body>
</html>