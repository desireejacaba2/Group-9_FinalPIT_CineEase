<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Movie;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Fetch the user's bookings
        $bookings = Booking::where('user_id', $id)->get();

        // Update the seats available for each booking's movie
        foreach ($bookings as $booking) {
            $movie = Movie::findOrFail($booking->movie_id);
            $movie->seats_available += $booking->seats_booked;
            $movie->save();

            // Delete the booking
            $booking->delete();
        }

        // Delete the user
        $user->delete();

        return redirect()->route('admin.manage-users')->with('success', 'User and their bookings deleted successfully.');
    }
}
