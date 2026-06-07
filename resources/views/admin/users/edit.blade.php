<x-app-layout>
    <x-slot name="header">
        <div class="max-w-3xl mx-auto">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Zarządzanie Użytkownikiem') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
                        <div class="bg-indigo-600 p-6">
                            <h3 class="text-white text-lg font-bold flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edytuj Profil: {{ $user->name }}
                            </h3>
                        </div>

                <div class="p-8">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pełne Imię i Nazwisko</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Adres E-mail</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Uprawnienia Systemowe</label>
                            <div class="relative">
                                <select name="is_admin" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 appearance-none bg-white py-2 px-3 transition duration-200">
                                    <option value="0" {{ !$user->is_admin ? 'selected' : '' }}>Standardowy Użytkownik</option>
                                    <option value="1" {{ $user->is_admin ? 'selected' : '' }}>Administrator Systemu</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                                &larr; Powrót do listy
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg shadow-indigo-200">
                                Zastosuj Zmiany
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center text-gray-400 text-xs mt-6">
                Ostatnia aktualizacja profilu: {{ $user->updated_at->diffForHumans() }}
            </p>
        </div>
    </div>
</x-app-layout>
