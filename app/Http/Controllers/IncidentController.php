<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $incidents = Incident::with(['creator', 'shift'])->orderBy('created_at', 'desc')->get();
        return view('incidents.index', compact('incidents'));
    }

    public function create()
    {
        $shifts = Shift::all();
        return view('incidents.create', compact('shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high',
            'status' => 'required|in:reported,investigating,resolved',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $incident = new Incident($validated);
        $incident->created_by = Auth::id();
        $incident->save();

        return redirect()->route('incidents.index')->with('success', 'Avārija veiksmīgi reģistrēta!');
    }

    public function show(Incident $incident)
    {
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        if ($incident->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $shifts = Shift::all();
        return view('incidents.edit', compact('incident', 'shifts'));
    }

    public function update(Request $request, Incident $incident)
    {
        if ($incident->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high',
            'status' => 'required|in:reported,investigating,resolved',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $incident->update($validated);

        return redirect()->route('incidents.index')->with('success', 'Avārija veiksmīgi atjaunināta!');
    }

    public function destroy(Incident $incident)
    {
        if ($incident->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $incident->delete();
        return redirect()->route('incidents.index')->with('success', 'Avārija veiksmīgi dzēsta!');
    }
}
