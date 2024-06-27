<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Manage Users') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-white underline">Dashboard</a>
                <a href="{{ route('admin.movies.create') }}" class="text-sm text-white underline">Add Movies</a>
                <a href="{{ route('admin.movies.back_rest') }}" class="text-sm text-white underline">Backup and Restore</a>

            </div>
        </div>
        
        <link rel="stylesheet" href="{{ asset('css/manageuser.css') }}">
    </x-slot>

    <div class="main-content">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="container">
                <div class="p-6 text-white"
                    <div>

                        <table class="table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: left;">ID</th>
                                    <th style="width: 20%; text-align: left;">Name</th>
                                    <th style="width: 20%; text-align: left;">Email</th>
                                    <th style="width: 15%; text-align: left;">Movie Title</th>
                                    <th style="width: 60%; text-align: left;">Seats Reserved</th>
                                    <th style="width: 10%; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @foreach ($user->bookings as $booking)
                                        <tr id="booking_{{ $booking->id }}">
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $booking->movie->title ?? 'N/A' }}</td>
                                            <td class="seat-arrangement-cell" style="padding-right: 30px;">
                                                {{ is_array($booking->seatArrangement) ? implode(', ', $booking->seatArrangement) : (is_string($booking->seatArrangement) ? $booking->seatArrangement : 'N/A') }}
                                            </td>
                                            <td style="text-align: right;">
                                                <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this booking?');" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm text-white underline" style="padding: 5px 10px;">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
