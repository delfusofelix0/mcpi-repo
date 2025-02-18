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

    public function update(Request $request, $id)
    {
        $position = WorkPosition::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $position->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Position updated successfully',
                'position' => $position
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Work position updated successfully.');
    }

    public function destroy(WorkPosition $workPosition)
    {
        try {
            $workPosition->delete();
            return response()->json(['success' => true, 'message' => 'Position deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the position'], 500);
        }
    }
}
