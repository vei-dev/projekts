<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Shift;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $reports = Report::with(['creator', 'shift', 'worker'])->orderBy('created_at', 'desc')->get();
        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        $shifts = Shift::all();
        $workers = Worker::all();
        return view('reports.create', compact('shifts', 'workers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'shift_id' => 'required|exists:shifts,id',
            'worker_id' => 'required|exists:workers,id',
            'hours_worked' => 'required|numeric|min:0|max:24',
        ]);

        $report = new Report($validated);
        $report->created_by = Auth::id();
        $report->save();

        return redirect()->route('reports.index')->with('success', 'Atskaite veiksmīgi izveidota!');
    }

    public function show(Report $report)
    {
        return view('reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        if ($report->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        $shifts = Shift::all();
        $workers = Worker::all();
        return view('reports.edit', compact('report', 'shifts', 'workers'));
    }

    public function update(Request $request, Report $report)
    {
        if ($report->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'comment' => 'required|string',
            'shift_id' => 'required|exists:shifts,id',
            'worker_id' => 'required|exists:workers,id',
            'hours_worked' => 'required|numeric|min:0|max:24',
        ]);

        $report->update($validated);

        return redirect()->route('reports.index')->with('success', 'Atskaite veiksmīgi atjaunināta!');
    }

    public function destroy(Report $report)
    {
        if ($report->created_by !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Atskaite veiksmīgi dzēsta!');
    }
}
