<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutController extends Controller
{
    public function index()
    {
        $abouts = About::orderBy('created_at', 'desc')->get();
        return view('admin.about.index', compact('abouts'));
    }

    public function create()
    {
        return view('admin.about.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->has('is_active')
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $directory = 'assets/images/about';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $image->move($publicPath, $filename);
            $data['image'] = $path;
        }

        About::create($data);

        return redirect()->route('admin.about.index')
            ->with('success', 'About content created successfully!');
    }

    public function edit($id)
    {
        $about = About::findOrFail($id);
        return view('admin.about.edit', compact('about'));
    }

    public function update(Request $request, $id)
    {
        $about = About::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->has('is_active')
        ];

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($about->image && file_exists(public_path($about->image))) {
                unlink(public_path($about->image));
            }

            $newImage = $request->file('image');
            $directory = 'assets/images/about';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $newImage->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $newImage->move($publicPath, $filename);
            $data['image'] = $path;
        }

        $about->update($data);

        return redirect()->route('admin.about.index')
            ->with('success', 'About content updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $about = About::findOrFail($id);

            // Delete image file
            if ($about->image && file_exists(public_path($about->image))) {
                unlink(public_path($about->image));
            }

            $about->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'About content deleted successfully!']);
            }

            return redirect()->route('admin.about.index')
                ->with('success', 'About content deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting about content: ' . $e->getMessage()], 500);
            }

            return redirect()->route('admin.about.index')
                ->with('error', 'Error deleting about content: ' . $e->getMessage());
        }
    }
}








