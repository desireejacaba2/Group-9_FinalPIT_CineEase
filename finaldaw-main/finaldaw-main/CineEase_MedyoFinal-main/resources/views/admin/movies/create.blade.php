<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Add Movies') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-white underline">Dashboard</a>
                <a href="{{ route('admin.manage-users') }}" class="text-sm text-white underline">Manage Users</a>
                <a href="{{ route('admin.movies.back_rest') }}" class="text-sm text-white underline">Backup and Restore</a>
           
            </div>
        </div>
            <link rel="stylesheet" href="{{ asset('css/addmovie.css') }}">
    </x-slot>

    <div class= "main-content">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="container">
                <div class="p-6 text-black">
                    <form action="{{ route('admin.movies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if ($errors->any())
                            <div class="mb-4">
                                <ul class="list-disc list-inside text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div>
                            <label for="title" class="block font-medium text-sm text-white">Title</label>
                            <input id="title" class="block mt-1 w-full" type="text" name="title" required autofocus>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-white">Description</label>
                            <textarea id="description" class="block mt-1 w-full" name="description" required></textarea>
                        </div>

                        <div class="mt-4">
                            <label for="date_showing" class="block font-medium text-sm text-white">Showing Date</label>
                            <input id="date_showing" class="block mt-1 w-full" type="date" name="date_showing" required>
                        </div>

                        <div class="mt-4">
                            <label for="amount" class="block font-medium text-sm text-white">Ticket Price</label>
                            <input id="amount" class="block mt-1 w-full" type="number" step="0.01" name="amount" required>
                        </div>

                        <div class="mt-4">
                            <label for="seats_available" class="block font-medium text-sm text-white">Seats Available</label>
                            <input id="seats_available" class="block mt-1 w-full" type="number" name="seats_available" required>
                        </div>

                        <div class="mt-4 text-white">
                            <label for="poster" class="block font-medium text-sm text-white">Poster</label>
                            <input id="poster" class="block mt-1 w-full" type="file" name="poster">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="add-button ml-4 inline-flex items-center px-4 py-2">
                                {{ __('Add Movie') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>