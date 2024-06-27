<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\SeatReservation;

class DashboardController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        return view('dashboard', compact('movies'));
    }
    public function showBookingPage($id)
    {
        $movie = Movie::findOrFail($id);
    
        // Fetch reserved and confirmed seats for the movie
        $bookings = Booking::where('movie_id', $id)
            ->whereIn('status', ['reserved', 'confirmed'])
            ->get(['seatArrangement', 'status']);
    
        $reservedSeats = $bookings->where('status', 'reserved')->flatMap(function ($booking) {
            return is_array($booking->seatArrangement) ? $booking->seatArrangement : json_decode($booking->seatArrangement, true);
        })->toArray();
    
        $confirmedSeats = $bookings->where('status', 'confirmed')->flatMap(function ($booking) {
            return is_array($booking->seatArrangement) ? $booking->seatArrangement : json_decode($booking->seatArrangement, true);
        })->toArray();
    
        return view('movies.book', compact('movie', 'reservedSeats', 'confirmedSeats'));
    }

    public function book($id)
    {
        $movie = Movie::findOrFail($id);
        $reservedSeats = SeatReservation::where('movie_id', $id)->where('status', 'reserved')->pluck('seatArrangement')->flatten();
        $confirmedSeats = SeatReservation::where('movie_id', $id)->where('status', 'confirmed')->pluck('seatArrangement')->flatten();
        return view('movies.book', compact('movie', 'reservedSeats', 'confirmedSeats'));
    }

    public function reserveSeat(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'seatArrangement' => 'required|array',
            'seatArrangement.*' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $movie = Movie::findOrFail($request->movie_id);
        $totalAmount = $request->quantity * $movie->amount;

        foreach ($request->seatArrangement as $seat) {
            $existingBooking = Booking::where('movie_id', $request->movie_id)
                ->whereJsonContains('seatArrangement', $seat)
                ->whereIn('status', ['reserved', 'confirmed'])
                ->exists();

            if ($existingBooking) {
                return redirect()->back()->with('error', 'One or more of the selected seats are already booked.');
            }
        }

        session([
            'booking' => [
                'user_id' => auth()->id(),
                'movie_id' => $request->movie_id,
                'movie_title' => $movie->title,
                'poster' => $movie->poster,
                'amount' => $movie->amount,
                'seatArrangement' => $request->seatArrangement,
                'quantity' => $request->quantity,
                'total_amount' => $totalAmount,
            ]
        ]);

        return redirect()->route('movies.proceed');
    }

    public function proceed()
    {
        $booking = session('booking');

        if (!$booking) {
            return redirect()->route('dashboard')->with('error', 'No booking data found.');
        }

        return view('movies.proceed', compact('booking'));
    }

    public function confirmBooking(Request $request)
    {
        $booking = session('booking');

        if (!$booking) {
            return redirect()->route('dashboard')->with('error', 'No booking data found.');
        }

        $request->validate([
            'payment_method' => 'required|string|in:credit_card,debit_card,paypal',
        ]);

        DB::beginTransaction();

        try {
            $createdBooking = Booking::create([
                'user_id' => auth()->id(),
                'movie_id' => $booking['movie_id'],
                'movie_title' => $booking['movie_title'],
                'poster' => $booking['poster'],
                'amount' => $booking['amount'],
                'seatArrangement' => json_encode($booking['seatArrangement']),
                'seats_booked' => $booking['quantity'],
                'total_amount' => $booking['total_amount'],
                'payment_method' => $request->payment_method,
                'status' => 'confirmed',
            ]);

            session()->forget('booking');

            DB::commit();

            return redirect()->route('movies.print.ticket', ['booking_id' => $createdBooking->id])->with('success', 'Booking confirmed!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing booking: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Failed to store booking.');
        }
    }

    public function printTicket($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        $user = Auth::user();
        return view('ticket.print', compact('booking', 'user'));
    }
}