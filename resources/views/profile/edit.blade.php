<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Profil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-2xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                Importy CSV
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Lista plików CSV, które dodałeś. Możesz usunąć cały import wraz z powiązanymi transakcjami.
                            </p>
                        </header>

                        @if($importedFiles->isEmpty())
                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                Nie masz jeszcze żadnych importów CSV.
                            </p>
                        @else
                            <div class="mt-6 space-y-3">
                                @foreach($importedFiles as $importedFile)
                                    <div class="flex flex-col gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $importedFile->file_name }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Dodano {{ $importedFile->created_at->format('d.m.Y H:i') }}
                                            </p>
                                        </div>

                                        <form method="post" action="{{ route('profile.imports.destroy', $importedFile) }}">
                                            @csrf
                                            @method('delete')
                                            <x-danger-button>
                                                Usuń import
                                            </x-danger-button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
