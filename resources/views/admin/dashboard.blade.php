<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-2xl text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-500 leading-tight">
                    Panel Administratora
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Przeglądaj użytkowników i zarządzaj aplikacją w jednym miejscu.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:scale-[1.01] hover:from-indigo-500 hover:to-purple-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Zarządzaj użytkownikami
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/90 p-6 shadow-sm backdrop-blur">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Wszyscy użytkownicy</p>
                            <p class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">{{ \App\Models\User::count() }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/90 p-6 shadow-sm backdrop-blur">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Administratorzy</p>
                            <p class="mt-2 text-3xl font-extrabold text-indigo-600 dark:text-indigo-300">{{ \App\Models\User::where('is_admin', true)->count() }}</p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-900/40 dark:text-purple-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.618A9 9 0 1112 2a9 9 0 0110.618 5.382z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.users.index') }}" class="block rounded-2xl border border-indigo-200 dark:border-indigo-800 bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-indigo-950/40 dark:via-gray-800 dark:to-purple-950/40 p-6 shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Szybki dostęp</p>
                    <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">Zarządzaj rolami, użytkownikami i uprawnieniami.</p>
                    <div class="mt-4 flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                        Kliknij, aby przejść do panelu użytkowników
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>