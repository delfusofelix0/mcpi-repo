<?php

namespace App\Http\Controllers;

use App\Models\WorkPosition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        WorkPosition::create($validated);

        return redirect()->back()->with('success', 'Position added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $position = WorkPosition::findOrFail($id);
        $position->update($request->only(['name', 'description']));

        return redirect()->back()->with('success', 'Position updated successfully');
    }

    public function destroy($id)
    {
        $position = WorkPosition::findOrFail($id);
        $position->delete();

        return redirect()->back()->with('success', 'Position deleted successfully');
    }
}
