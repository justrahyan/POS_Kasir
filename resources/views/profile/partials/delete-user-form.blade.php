<section class="space-y-6" x-data="{ showConfirm: false }">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Hapus Akun
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.
        </p>
    </header>

    <button @click="showConfirm = true" type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
        Hapus Akun
    </button>
    
    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showConfirm" 
         x-cloak 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50" 
         @keydown.escape.window="showConfirm = false">
        
        <div @click.away="showConfirm = false" 
             x-show="showConfirm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900">
                    Apakah Anda yakin ingin menghapus akun Anda?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun Anda secara permanen.
                </p>

                <div class="mt-6">
                    <label for="password_delete" class="sr-only">Password</label>
                    <input id="password_delete" name="password" type="password" class="block w-3/4 px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm" placeholder="Password" />
                    @error('password', 'userDeletion')
                        {{-- Tampilkan modal lagi jika ada error password --}}
                        <script> document.addEventListener('DOMContentLoaded', () => { new Alpine(document.querySelector('[x-data]')).showConfirm = true; }); </script>
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showConfirm = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Hapus Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
