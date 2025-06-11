<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;

class WorkerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Worker::class);
        $workers = Worker::all();
        return view('workers.index', compact('workers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Worker::class);
        return view('workers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Worker::class);
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:workers',
            'password' => 'required|confirmed|min:8',
        ]);

        $worker = Worker::create($validated);

        return redirect()->route('workers.index')
                        ->with('success', 'Worker created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Worker $worker)
    {
        $this->authorize('view', $worker);
        return view('workers.show',compact('worker'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Worker $worker)
    {
        $this->authorize('update', $worker);
        return view('workers.edit',compact('worker'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Worker $worker)
    {
        $this->authorize('update', $worker);
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:workers,email,'.$worker->id,
            'password' => 'nullable|confirmed|min:8',
        ]);

        if($request->filled('password')){
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $worker->update($validated);

        return redirect()->route('workers.index')
                        ->with('success', 'Worker updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worker $worker)
    {
        $this->authorize('delete', $worker);
        $worker->delete();

        return redirect()->route('workers.index')
                        ->with('success', 'Worker deleted successfully');
    }
}
