<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
        <div>
            <h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                Create your account
            </h2>
        </div>
        <form wire:submit="register" class="mt-8 space-y-6">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="name" class="sr-only">Name</label>
                    <input wire:model="name" id="name" type="text" required 
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Name">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input wire:model="email" id="email" type="email" required
                        class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-700 placeholder-gray-500 text-gray-900 dark:text-white dark:bg-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                        placeholder="Email address">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Register
                </button>
            </div>
        </form>
    </div>
</div> 