<x-app-layout>
    <x-slot name="header">
        <!-- Navigation Links -->
        <div class="navigation-links">
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
        </div>

        <!-- Link to dash.css -->
        <link rel="stylesheet" href="{{ asset('css/userdash.css') }}">
    </x-slot>

    <div class="py-5 main-content" style="background-color: black; color: white;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg content-wrapper">
                <div class="p-6 text-gray-900 content-inner">
                    <h2 class="section-title">Movie Lists</h2>

                    <!-- Movies Container -->
                    <div class="movies-container">
                        <!-- Loop through movies -->
                        @foreach ($movies as $movie)
                            <div class="movie-item">
                                <img src="{{ asset('storage/' . $movie->poster) }}" alt="Movie Poster" class="poster">
                                <div class="details">
                                    <h3 class="title">{{ $movie->title }}</h3>
                                    <p class="description">{{ $movie->description }}</p>
                                    <p class="seats"> Seats Available: {{ $movie->seats_available }} </p>
                                    <p class="year">Date Released: <span class="font-medium;">{{ \Carbon\Carbon::parse($movie->date_showing)->format('F j, Y') }}</span></p>
                                    <p class="price">Price: ₱ <span class="font-medium">{{ $movie->amount }}</span></p>
                                    @if ($movie->seats_available > 0)
                                        <a href="{{ route('movies.book', ['id' => $movie->id]) }}" class="book-now-button">Book Now</a>
                                    @else
                                    <span class="book-now-button fully-booked">Fully Booked</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
