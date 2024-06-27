<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Movie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class MovieController extends Controller
{
    public function showBookingPage($id)
    {
        $movie = Movie::findOrFail($id);
        $totalSeatsAvailable = $this->getTotalSeatsAvailable($id);
        $reservedSeats = Booking::where('movie_id', $id)
            ->whereIn('status', ['reserved', 'confirmed'])
            ->get()
            ->flatMap(function ($booking) {
                return is_array($booking->seatArrangement) ? $booking->seatArrangement : json_decode($booking->seatArrangement, true);
            })
            ->toArray();

        return view('movies.book', compact('movie', 'reservedSeats'));
    }

    public function reserveSeat(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'seatArrangement' => 'required|array|min:1',
            'quantity' => 'required|integer|min:1',
        ]);

        $movie = Movie::findOrFail($validated['movie_id']);

        if ($validated['quantity'] > $movie->seats_available) {
            return redirect()->back()->withErrors(['quantity' => 'You cannot reserve more seats than available.']);
        }

        $reservedSeats = Booking::where('movie_id', $movie->id)
            ->whereIn('status', ['reserved', 'confirmed'])
            ->get()
            ->flatMap(function ($booking) {
                return is_array($booking->seatArrangement) ? $booking->seatArrangement : json_decode($booking->seatArrangement, true);
            })
            ->toArray();

        $selectedSeats = $validated['seatArrangement'];
        foreach ($selectedSeats as $seat) {
            if (in_array($seat, $reservedSeats)) {
                return redirect()->back()->withErrors(['seatArrangement' => 'One or more selected seats are already reserved.']);
            }
        }

        $totalAmount = $validated['quantity'] * $movie->amount;

        session([
            'booking' => [
                'user_id' => auth()->id(),
                'movie_id' => $movie->id,
                'movie_title' => $movie->title,
                'poster' => $movie->poster,
                'seatArrangement' => $selectedSeats,
                'quantity' => $validated['quantity'],
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

        \Log::info('Booking data in confirmBooking:', ['booking' => $booking]);
        \Log::info('Request data in confirmBooking:', $request->all());

        if (!$booking) {
            return redirect()->route('dashboard')->with('error', 'No booking data found.');
        }

        $request->validate([
            'payment_method' => 'required|string|in:credit_card,debit_card,paypal',
        ]);

        DB::beginTransaction();

        try {
            Booking::create([
                'user_id' => auth()->id(),
                'movie_id' => $booking['movie_id'],
                'movie_title' => $booking['movie_title'],
                'poster' => $booking['poster'],
                'seatArrangement' => $booking['seatArrangement'],
                'seats_booked' => $booking['quantity'],
                'total_amount' => $booking['total_amount'],
                'payment_method' => $request->payment_method,
            ]);

            session()->forget('booking');

            DB::commit();

            return redirect()->route('movies.print.ticket')->with('success', 'Booking confirmed!');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Error storing booking: ' . $e->getMessage());

            return redirect()->route('dashboard')->with('error', 'Failed to store booking.');
        }
    }

    public function create()
    {
        return view('admin.movies.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'poster' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'date_showing' => 'required|date',
            'amount' => 'required|numeric',
            'seats_available' => 'required|integer',
        ]);

        $posterPath = $request->file('poster')->store('posters', 'public');

        $movie = new Movie([
            'title' => $validatedData['title'],
            'poster' => $posterPath,
            'description' => $validatedData['description'],
            'date_showing' => $validatedData['date_showing'],
            'amount' => $validatedData['amount'],
            'seats_available' => $validatedData['seats_available'],
        ]);
        $movie->save();

        return redirect()->route('admin.dashboard')->with('success', 'Movie added successfully.');
    }

    public function edit(Movie $movie)
    {
        return view('admin.movies.edit', compact('movie'));
    }

    public function update(Request $request, Movie $movie)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'date_showing' => 'required|date',
            'amount' => 'required|numeric',
            'seats_available' => 'required|integer',
        ]);

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
            $movie->poster = $posterPath;
        }

        $movie->title = $validatedData['title'];
        $movie->description = $validatedData['description'];
        $movie->date_showing = $validatedData['date_showing'];
        $movie->amount = $validatedData['amount'];
        $movie->seats_available = $validatedData['seats_available'];
        $movie->save();

        return redirect()->route('admin.dashboard')->with('success', 'Movie updated successfully.');
    }

    public function printTicket()
    {
        $booking = session('booking');

        if (!$booking) {
            return redirect()->route('dashboard')->with('error', 'No booking data found.');
        }

        return view('movies.print-ticket', compact('booking'));
    }
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Movie deleted successfully!');
    }
    public function getTotalSeatsAvailable($movieId)
{
    $results = DB::select('CALL CalculateTotalSeatsAvailable(?, @total_seats)', [$movieId]);
    $totalSeats = DB::select('SELECT @total_seats AS total_seats')[0]->total_seats;

    return $totalSeats;
}

}