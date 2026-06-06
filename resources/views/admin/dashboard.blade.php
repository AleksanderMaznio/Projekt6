<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Administratora') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Wszyscy użytkownicy</h3>
                    <p class="text-3xl font-bold">{{ \App\Models\User::count() }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-gray-500">Administratorzy</h3>
                    <p class="text-3xl font-bold text-indigo-600">{{ \App\Models\User::where('is_admin', true)->count() }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow flex items-center justify-center">
                    <a href="{{ route('admin.users.index') }}" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                        Zarządzaj użytkownikami
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>