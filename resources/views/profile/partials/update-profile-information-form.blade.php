<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Informacje o profilu
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Zaktualizuj swoje dane profilowe oraz adres e-mail.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>  
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
            @csrf   
    @method('patch')

    <div class="mt-4">
        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
            @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover" alt="Zdjęcie profilowe">
            @else
                <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            @endif
        </div>

        <div class="mt-3">
            <label for="avatar" class="inline-flex cursor-pointer rounded-full border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                Wybierz zdjęcie
            </label>
            <input id="avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/jpg,image/webp" class="sr-only" />
            <p class="mt-2 text-xs text-gray-500">PNG, JPG lub WEBP, do 2 MB.</p>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>
    </div>
        <div>
            <x-input-label for="name" :value="__('Imię i nazwisko')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Adres e-mail')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Twój adres e-mail nie został jeszcze zweryfikowany.

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Kliknij tutaj, aby wysłać ponownie wiadomość weryfikacyjną.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            Nowy link weryfikacyjny został wysłany na Twój adres e-mail.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Zapisz</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >Zapisano.</p>
            @endif
        </div>
    </form>
</section>
