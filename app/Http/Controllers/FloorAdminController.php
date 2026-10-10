<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class FloorAdminController extends Controller
{
    public function index(Request $request)
    {
        $floors = Floor::orderBy('building')->orderBy('name')->get();
        $editFloor = null;

        if ($request->filled('edit')) {
            $editFloor = Floor::find($request->input('edit'));
        }

        return view('floors.manage', [
            'floors' => $floors,
            'editFloor' => $editFloor,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $floor = Floor::create($data);

        return redirect()
            ->route('admin.floors.index')
            ->with('status', 'Floor created successfully.');
    }

    public function update(Request $request, Floor $floor)
    {
        $data = $this->validatedData($request, $floor);

        $floor->update($data);

        return redirect()
            ->route('admin.floors.index')
            ->with('status', 'Floor updated successfully.');
    }

    public function destroy(Floor $floor)
    {
        $floor->delete();

        return redirect()
            ->route('admin.floors.index')
            ->with('status', 'Floor deleted.');
    }

    protected function validatedData(Request $request, ?Floor $floor = null): array
    {
        $building = $floor?->building ?? 'Main Building';

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'width' => ['nullable', 'integer', 'min:1'],
            'height' => ['required', 'integer', 'min:1'],
            'glb_file' => ['nullable', 'file', 'max:102400'], // GLB has non-standard mime; accept any file up to 100MB
            'stairs_model_x' => ['nullable', 'numeric'],
            'stairs_model_y' => ['nullable', 'numeric'],
            'stairs_model_z' => ['nullable', 'numeric'],
            'you_are_here_model_x' => ['nullable', 'numeric'],
            'you_are_here_model_y' => ['nullable', 'numeric'],
            'you_are_here_model_z' => ['nullable', 'numeric'],
            'routing_mode' => ['nullable', 'string', 'in:you_are_here,stairs'],
        ]);

        // Start with existing paths if editing
        $imagePath = $floor?->image_path ?? 'storage/floors/ground.svg'; // legacy fallback, not used in kiosk
        $glbPath = $floor?->glb_path;

        // Handle GLB upload (stored directly under public/kiosk/assets)
        if ($request->hasFile('glb_file')) {
            $file = $request->file('glb_file');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $targetDir = public_path('kiosk/assets');

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            $file->move($targetDir, $filename);
            $glbPath = '/kiosk/assets/' . $filename;
        }

        $routingMode = $validated['routing_mode'] ?? 'you_are_here';
        $useStairs = $routingMode === 'stairs';
        $showYouAreHere = $routingMode !== 'stairs';

        return [
            'building' => $building,
            'name' => $validated['name'],
            'image_path' => $imagePath ?? 'storage/floors/ground.svg',
            'width' => $validated['width'] ?? null,
            'height' => $validated['height'],
            'glb_path' => $glbPath,
            'stairs_model_x' => $validated['stairs_model_x'] ?? null,
            'stairs_model_y' => $validated['stairs_model_y'] ?? null,
            'stairs_model_z' => $validated['stairs_model_z'] ?? null,
            'you_are_here_model_x' => $validated['you_are_here_model_x'] ?? null,
            'you_are_here_model_y' => $validated['you_are_here_model_y'] ?? null,
            'you_are_here_model_z' => $validated['you_are_here_model_z'] ?? null,
            'use_stairs_as_start' => $useStairs,
            'show_you_are_here' => $showYouAreHere,
        ];
    }
}



