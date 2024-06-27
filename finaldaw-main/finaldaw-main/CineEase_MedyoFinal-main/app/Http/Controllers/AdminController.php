<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Movie;
use App\Models\Booking;

class AdminController extends Controller
{
    public function index()
{
    // Fetch all movies with selected columns
    $movies = Movie::select('id', 'title', 'poster', 'description', 'date_showing', 'amount', 'seats_available')->get();
    return view('admindash', compact('movies'));
}

    public function manageUsers()
    {
        // Fetch all reserved and confirmed seats
        $bookings = Booking::whereIn('status', ['reserved', 'confirmed'])->select('seatArrangement')->get();
        $reservedSeats = $bookings->flatMap(function ($booking) {
            return is_array($booking->seatArrangement) ? $booking->seatArrangement : json_decode($booking->seatArrangement, true);
        })->toArray();

        // Fetch all users with their bookings and select necessary columns
        $users = User::select('id', 'name', 'email')->with(['bookings' => function ($query) {
            $query->select('id', 'user_id', 'movie_id', 'seatArrangement')->with(['movie' => function ($query) {
                $query->select('id', 'title');
            }]);
        }])->get();

        return view('admin.manage-users', compact('reservedSeats', 'users'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.manage-users')->with('success', 'User deleted successfully.');
    }

    public function updateBooking(Request $request)
    {
        // Validate and update booking logic here
        $validated = $request->validate([
            'seatArrangement' => 'required|array',
            'booking_id' => 'required|integer|exists:bookings,id',
        ]);

        $booking = Booking::findOrFail($validated['booking_id']);
        $booking->seatArrangement = $validated['seatArrangement'];
        $booking->save();

        return redirect()->route('admin.manage-users')->with('success', 'Booking updated successfully.');
    }

    public function back_rest()
    {
        return view('admin.movies.back_rest');
    }
}