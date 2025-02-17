<?php

namespace App\Http\Controllers;

use App\Models\WorkPosition;
use Illuminate\Http\Request;

class WorkPositionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        WorkPosition::create($request->only('name', 'description'));

        return redirect()->route('dashboard')->with('success', 'Work position created successfully.');
    }

    public function edit(WorkPosition $workPosition)
    {
        return view('work-position.edit', compact('workPosition'));
    }

    public function update(Request $request, WorkPosition $workPosition)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $workPosition->update($request->only('name', 'description'));

        return redirect()->route('dashboard')->with('success', 'Work position updated successfully.');
    }

    public function destroy(WorkPosition $workPosition)
    {
        try {
            $workPosition->delete();
            return redirect()->route('dashboard')->with('success', 'Work position deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Failed to delete work position: ' . $e->getMessage());
        }
    }
}
