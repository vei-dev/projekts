<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || (!$user->hasRole('admin') && !$user->hasRole('dispatcher'))) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    public function index()
    {
        $shifts = Shift::with('creator')->orderBy('date', 'desc')->get();
        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'shift_type' => 'required|in:day,night',
            'location' => 'required|string|max:255',
        ]);

        $shift = new Shift($validated);
        $shift->created_by = Auth::id();
        $shift->save();

        return redirect()->route('shifts.index')->with('success', 'Maiņa veiksmīgi izveidota!');
    }

    public function show(Shift $shift)
    {
        return view('shifts.show', compact('shift'));
    }

    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'shift_type' => 'required|in:day,night',
            'location' => 'required|string|max:255',
        ]);

        $shift->update($validated);

        return redirect()->route('shifts.index')->with('success', 'Maiņa veiksmīgi atjaunināta!');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('shifts.index')->with('success', 'Maiņa veiksmīgi dzēsta!');
    }
}
