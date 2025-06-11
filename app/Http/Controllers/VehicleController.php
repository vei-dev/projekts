<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $vehicles = Vehicle::with(['creator', 'shift'])->orderBy('created_at', 'desc')->get();
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $shifts = Shift::all();
        return view('vehicles.create', compact('shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'status' => 'required|in:aizņemts,izmanto,remonts',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        $vehicle = new Vehicle($validated);
        $vehicle->created_by = Auth::id();
        $vehicle->save();

        return redirect()->route('vehicles.index')->with('success', 'Transports veiksmīgi izveidots!');
    }

    public function show(Vehicle $vehicle)
    {
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        if ($vehicle->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $shifts = Shift::all();
        return view('vehicles.edit', compact('vehicle', 'shifts'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'license_plate' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'status' => 'required|in:aizņemts,izmanto,remonts',
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Transports veiksmīgi atjaunināts!');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Transports veiksmīgi dzēsts!');
    }
}
