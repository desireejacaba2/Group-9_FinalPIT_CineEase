<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Edit Movie') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-white underline">Dashboard</a>
                <a href="{{ route('admin.manage-users') }}" class="text-sm text-white underline">Manage Users</a>
                <a href="{{ route('admin.movies.back_rest') }}" class="text-sm text-white underline">Backup and Restore</a>
           
            </div>
        </div>
            <link rel="stylesheet" href="{{ asset('css/editmovie.css') }}">
    </x-slot>

<div class="main-content">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class= "container">
                <div class="p-6 text-black">
                    <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="title" class="block font-medium text-sm text-white">Title</label>
                            <input id="title" class="block mt-1 w-full" type="text" name="title" value="{{ $movie->title }}" required autofocus>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-white">Description</label>
                            <textarea id="description" class="block mt-1 w-full" name="description" required>{{ $movie->description }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label for="date_showing" class="block font-medium text-sm text-white">Showing Date</label>
                            <input id="date_showing" class="block mt-1 w-full" type="date" name="date_showing" value="{{ $movie->date_showing }}" required>
                        </div>

                        <div class="mt-4">
                            <label for="amount" class="block font-medium text-sm text-white">Ticket Price</label>
                            <input id="amount" class="block mt-1 w-full" type="number" step="0.01" name="amount" value="{{ $movie->amount }}" required>
                        </div>

                        <div class="mt-4">
                            <label for="seats_available" class="block font-medium text-sm text-white">Seats Available</label>
                            <input id="seats_available" class="block mt-1 w-full" type="number" name="seats_available" value="{{ $movie->seats_available }}" required>
                        </div>

                        <div class="mt-4 text-white">
                            <label for="poster" class="block font-medium text-sm text-white">Poster</label>
                            <input id="poster" class="block mt-1 w-full" type="file" name="poster">
                            <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}" class="mt-2" style="width: 100px; height: auto;">
                        </div>

                        <div>
    <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-end mt-4 space-x-4">
            <button type="submit" class="update-button px-4 py-2">
                {{ __('Update Movie') }}
            </button>

            <button type="button" onclick="confirmDelete()" class="delete-button px-4 py-2">
                {{ __('Delete Movie') }}
            </button>
        </div>
    </form>

    <form id="deleteForm" action="{{ route('admin.movies.destroy', $movie->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>
                       
            </div>
        </div>
    </div>
    </div>

    <script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this movie?')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
</x-app-layout>
