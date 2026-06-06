<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SubTracker - Twój asystent finansowy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        
        <nav class="p-6 flex justify-between items-center max-w-7xl mx-auto w-full">
            <div class="flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <span class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500">
                    SubTracker
                </span>
            </div>
            <div>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-indigo-500 dark:text-gray-400 dark:hover:text-indigo-400 transition">Mój Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-indigo-500 dark:text-gray-400 dark:hover:text-indigo-400 transition mr-4">Zaloguj się</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-5 rounded-lg shadow-md transition-transform transform hover:-translate-y-0.5">Zarejestruj się</a>
                        @endif
                    @endauth
                @endif
            </div>
        </nav>

        <main class="flex-grow flex flex-col justify-center items-center text-center px-4 sm:px-6 lg:px-8 mt-10 mb-20">
            <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight mb-6">
                Przejmij kontrolę nad swoimi <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500">wydatkami i subskrypcjami</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mb-10">
                Wgraj swój wyciąg bankowy, a nasz algorytm przeanalizuje Twoje transakcje, wyłapie cykliczne płatności i pokaże Ci, na co naprawdę uciekają Twoje pieniądze.
            </p>
            
            <div class="flex gap-4 justify-center">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-lg rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        Przejdź do analizy
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-lg rounded-xl shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                        Rozpocznij za darmo
                    </a>
                @endauth
            </div>
        </main>

        <div class="py-16 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-10 text-center">
                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
                    <div class="flex justify-center items-center w-14 h-14 mx-auto bg-indigo-100 dark:bg-indigo-900/50 text-indigo-500 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Prosty import CSV</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bezpiecznie wgrywaj dane z wyciągów bankowych. Brak konieczności łączenia konta przez API.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
                    <div class="flex justify-center items-center w-14 h-14 mx-auto bg-purple-100 dark:bg-purple-900/50 text-purple-500 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Automatyczna Detekcja</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Algorytm ekonometryczny wykrywa cykliczne płatności i przypisuje je do subskrypcji.</p>
                </div>
                <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
                    <div class="flex justify-center items-center w-14 h-14 mx-auto bg-green-100 dark:bg-green-900/50 text-green-500 rounded-full mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Oszczędności</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Wizualizuj swoje wydatki, odnajduj ukryte koszty i decyduj, z których usług zrezygnować.</p>
                </div>
            </div>
        </div>

        <footer class="p-6 text-center text-sm font-semibold text-gray-400 dark:text-gray-600 border-t border-gray-200 dark:border-gray-800">
            &copy; {{ date('Y') }} Projekt Zaliczeniowy
        </footer>
        
    </div>
</body>
</html>