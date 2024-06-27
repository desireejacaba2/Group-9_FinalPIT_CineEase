<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.manage-users') }}" class="text-sm text-white underline">Manage Users</a>
                <a href="{{ route('admin.movies.create') }}" class="text-sm text-white underline">Add Movies</a>
                <a href="{{ route('admin.movies.back_rest') }}" class="text-sm text-white underline">Backup and Restore</a>
            </div>
        </div>
        <link rel="stylesheet" href="{{ asset('css/admindash.css') }}">
    </x-slot>

    <div class="main-content">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">
                <div class="p-6 text-white">
                    <table class="table" style="width: 100%;">
                        <thead>
                            <tr>
                                    <th style="width: 5%; text-align: left;">ID</th>
                                    <th style="width: 10%; text-align: left;">Title</th>
                                    <th style="width: 10%; text-align: left;">Poster</th>
                                    <th style="width: 30%; text-align: left;">Description</th>
                                    <th style="width: 15%; text-align: left; padding-left: 30px;">Showing Date</th>
                                    <th style="width: 10%; text-align: left;">Seats Available</th>
                                    <th style="width: 20%; text-align: right; padding-right: 30px;">Ticket Price</th>
                                    <th style="width: 30%; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movies as $movie)
                                <tr id="movie_{{ $movie->id }}">
                                    <td>{{ $movie->id }}</td>
                                    <td>{{ $movie->title }}</td>
                                    <td><img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}" class="w-20 h-20"></td>
                                    <td style= "text-align: justify;">{{ $movie->description }}</td>
                                    <td style= "padding-left: 30px;">{{ \Carbon\Carbon::parse($movie->date_showing)->format('F j, Y') }}</td>
                                    <td>{{ $movie->seats_available }}</td>
                                    <td style="text-align: right; padding-right: 50px;">₱ {{ number_format($movie->amount, 2) }}</td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('admin.movies.edit', $movie->id) }}" class="text-sm text-white underline" style="padding-right: 30px;">Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
