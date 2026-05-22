<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>ELI - Explain Like I'm 5</title>
    @vite(['resources/css/app.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0,1" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #000000;
            --bg-secondary: #0a0a0a;
            --bg-tertiary: #111111;
            --text-primary: #ffffff;
            --text-secondary: #e0e0e0;
            --text-muted: #888888;
            --border: #2a2a2a;
            --border-light: #3a3a3a;
            --accent: #3b82f6;
            --accent-hover: #60a5fa;
            --accent-green: #22c55e;
            --accent-yellow: #eab308;
            --accent-purple: #a855f7;
            --accent-red: #ef4444;
            --accent-orange: #f97316;
            --card-bg: #0d0d0d;
            --hover-bg: #1a1a1a;
        }

        body.light-mode {
            --bg-primary: #f0f0f0;
            --bg-secondary: #ffffff;
            --bg-tertiary: #e8e8e8;
            --text-primary: #000000;
            --text-secondary: #333333;
            --text-muted: #666666;
            --border: #d0d0d0;
            --border-light: #c0c0c0;
            --accent: #2563eb;
            --accent-hover: #3b82f6;
            --card-bg: #ffffff;
            --hover-bg: #f5f5f5;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.2s ease;
            font-family: 'Consolas', monospace, system-ui;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--text-muted);
            border-radius: 3px;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
            font-size: 20px;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div style="display: flex; width: 100%; height: 100vh; overflow: hidden;">
        @yield('content')
    </div>

    <script>
        // Global theme functions
        function toggleTheme() {
            document.body.classList.toggle('light-mode');
            const isLight = document.body.classList.contains('light-mode');
            localStorage.setItem('eli_theme', isLight ? 'light' : 'dark');
            
            // Update all theme icons on the page
            document.querySelectorAll('#theme-icon, .theme-icon').forEach(icon => {
                if (icon) icon.textContent = isLight ? 'light_mode' : 'dark_mode';
            });
        }
        
        function updateThemeIcon() {
            const isLight = document.body.classList.contains('light-mode');
            document.querySelectorAll('#theme-icon, .theme-icon').forEach(icon => {
                if (icon) icon.textContent = isLight ? 'light_mode' : 'dark_mode';
            });
        }
        
        // Load saved theme
        const savedTheme = localStorage.getItem('eli_theme');
        if (savedTheme === 'light') {
            document.body.classList.add('light-mode');
        }
        
        // Update icons on load
        document.addEventListener('DOMContentLoaded', function() {
            updateThemeIcon();
        });
        
        // Watch for theme changes
        const themeObserver = new MutationObserver(function() {
            updateThemeIcon();
        });
        themeObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    </script>
</body>
</html>