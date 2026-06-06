<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista Użytkowników') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b p-2">ID</th>
                            <th class="border-b p-2">Imię</th>
                            <th class="border-b p-2">Email</th>
                            <th class="border-b p-2">Rola</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="border-b p-2">{{ $user->id }}</td>
                            <td class="border-b p-2">{{ $user->name }}</td>
                            <td class="border-b p-2">{{ $user->email }}</td>
                            <td class="border-b p-2">
                                @if($user->is_admin)
                                    <span class="text-red-500 font-bold">Admin</span>
                                @else
                                    <span class="text-green-500">Użytkownik</span>
                                @endif
                            </td>
                            <td class="border-b p-2">
    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-500 mr-2">Edytuj</a>
    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button class="text-red-500" onclick="return confirm('Na pewno?')">Usuń</button>
    </form>
</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>