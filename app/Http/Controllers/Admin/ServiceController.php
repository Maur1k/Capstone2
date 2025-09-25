<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceStep;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Services
    public function index()
    {
        $services = Service::orderBy('name')->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'], // 10MB max
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Handle file upload
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/icons', $filename);
            $data['icon_path'] = '/storage/icons/' . $filename;
        }

        Service::create($data);
        return redirect()->route('admin.services.index')->with('status', 'Service created.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'], // 10MB max
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Handle file upload
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/icons', $filename);
            $data['icon_path'] = '/storage/icons/' . $filename;
        }

        $service->update($data);
        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    // Types
    public function types(Service $service)
    {
        $types = $service->types()->orderBy('name')->get();
        return view('admin.services.types', compact('service', 'types'));
    }

    public function typeStore(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
        ]);

        $service->types()->create($data);
        return back()->with('status', 'Type added.');
    }

    public function typeUpdate(Request $request, Service $service, ServiceType $type)
    {
        abort_unless($type->service_id === $service->id, 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $type->update($data);
        return back()->with('status', 'Type updated.');
    }

    public function typeDestroy(Service $service, ServiceType $type)
    {
        abort_unless($type->service_id === $service->id, 404);
        $type->delete();
        return back()->with('status', 'Type deleted.');
    }

    // Steps
    public function steps(Service $service, ServiceType $type)
    {
        abort_unless($type->service_id === $service->id, 404);
        $steps = $type->steps()->orderBy('display_order')->get();
        return view('admin.services.steps', compact('service', 'type', 'steps'));
    }

    public function stepStore(Request $request, Service $service, ServiceType $type)
    {
        abort_unless($type->service_id === $service->id, 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);
        $type->steps()->create($data + ['display_order' => $type->steps()->max('display_order') + 1]);
        return back()->with('status', 'Step added.');
    }

    public function stepUpdate(Request $request, Service $service, ServiceType $type, ServiceStep $step)
    {
        abort_unless($type->service_id === $service->id && $step->service_type_id === $type->id, 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $step->update($data);
        return back()->with('status', 'Step updated.');
    }

    public function stepDestroy(Service $service, ServiceType $type, ServiceStep $step)
    {
        abort_unless($type->service_id === $service->id && $step->service_type_id === $type->id, 404);
        $step->delete();
        return back()->with('status', 'Step deleted.');
    }
}


