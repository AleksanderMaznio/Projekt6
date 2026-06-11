<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Import danych') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="text-center mb-6">
                        <svg class="mx-auto h-10 w-10 text-indigo-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <h3 class="text-xl font-bold tracking-tight">Wgraj wyciąg CSV</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">System automatycznie wyłapie ukryte subskrypcje.</p>
                    </div>

                    <form id="import-form" action="/import" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf 
                        
                        <div class="flex justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 hover:bg-gray-100 transition-all duration-200 group">
                                <div class="flex flex-col items-center justify-center pt-4 pb-4">
                                    <svg class="w-8 h-8 mb-3 text-gray-400 group-hover:text-indigo-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="mb-1 text-base text-gray-500 dark:text-gray-400"><span class="font-semibold">Wybierz plik</span> lub przeciągnij</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 uppercase tracking-widest">.CSV (max. 2MB)</p>
                                </div>
                                <input id="dropzone-file" type="file" name="csv_file" accept=".csv,text/csv" class="hidden" required />
                            </label>
                        </div>

                        <p id="file-help" class="text-center text-sm text-gray-500 dark:text-gray-400">Dozwolony jest tylko plik CSV.</p>
                        
                        <div class="flex justify-center mt-5">
                            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-md transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 w-full">
                                Rozpocznij analizę
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('dropzone-file');
        const importForm = document.getElementById('import-form');
        const fileHelp = document.getElementById('file-help');

        const setFileHelp = (message, isError = false) => {
            fileHelp.textContent = message;
            fileHelp.className = isError
                ? 'text-center text-sm text-red-500 dark:text-red-400'
                : 'text-center text-sm text-gray-500 dark:text-gray-400';
        };

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const textElement = this.parentElement.querySelector('p.text-base');

            if (!file) {
                textElement.innerHTML = '<span class="font-semibold">Wybierz plik</span> lub przeciągnij';
                setFileHelp('Dozwolony jest tylko plik CSV.');
                return;
            }

            const isCsv = file.name.toLowerCase().endsWith('.csv') || file.type.includes('csv');

            if (!isCsv) {
                this.value = '';
                textElement.innerHTML = '<span class="font-semibold">Wybierz plik</span> lub przeciągnij';
                setFileHelp('Nieprawidłowy typ pliku. Wgraj wyłącznie plik CSV.', true);
                return;
            }

            textElement.innerHTML = `<span class="font-semibold text-indigo-500">Wybrano:</span> ${file.name}`;
            setFileHelp('Plik gotowy do importu.');
        });

        importForm.addEventListener('submit', function(e) {
            const file = fileInput.files[0];
            const isCsv = file && (file.name.toLowerCase().endsWith('.csv') || file.type.includes('csv'));

            if (!isCsv) {
                e.preventDefault();
                setFileHelp('Nieprawidłowy typ pliku. Wgraj wyłącznie plik CSV.', true);
            }
        });
    </script>
</x-app-layout>