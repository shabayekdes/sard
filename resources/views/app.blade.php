<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if (app()->isProduction())
        <script type="text/javascript">
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "uzzmvffhqo");
        </script>
        @endif

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap"
            rel="stylesheet"
        />

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        @routes
        @if (app()->environment('local'))
            @viteReactRefresh
        @endif
        @vite(['resources/js/app.tsx'])
        <script>
            // Base URL from current request (central or tenant domain) for Inertia/Ziggy/assets
            window.baseUrl = '{{ url('/') }}';
            window.APP_URL = '{{ url('/') }}';

            // Set initial locale for i18next
            {{--fetch('{{ route("initial-locale") }}')--}}
            {{--    .then(response => response.text())--}}
            {{--    .then(locale => {--}}
            {{--        window.initialLocale = locale;--}}
            {{--    })--}}
            {{--    .catch(() => {--}}
            {{--        window.initialLocale = 'en';--}}
            {{--    });--}}
        </script>
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
