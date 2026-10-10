<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->get();
        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'author' => $request->author,
            'published_at' => $request->published_at ? date('Y-m-d H:i:s', strtotime($request->published_at)) : null,
            'is_published' => $request->has('is_published')
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $directory = 'assets/images/blog';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $image->move($publicPath, $filename);
            $data['image'] = $path;
        }

        Blog::create($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post created successfully!');
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return view('admin.blog.edit', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'is_published' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'author' => $request->author,
            'published_at' => $request->published_at ? date('Y-m-d H:i:s', strtotime($request->published_at)) : null,
            'is_published' => $request->has('is_published')
        ];

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($blog->image && file_exists(public_path($blog->image))) {
                unlink(public_path($blog->image));
            }

            $newImage = $request->file('image');
            $directory = 'assets/images/blog';
            $publicPath = public_path($directory);
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $newImage->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            $newImage->move($publicPath, $filename);
            $data['image'] = $path;
        }

        $blog->update($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Blog post updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            // Delete image file
            if ($blog->image && file_exists(public_path($blog->image))) {
                unlink(public_path($blog->image));
            }

            $blog->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Blog post deleted successfully!']);
            }

            return redirect()->route('admin.blog.index')
                ->with('success', 'Blog post deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting blog post: ' . $e->getMessage()], 500);
            }

            return redirect()->route('admin.blog.index')
                ->with('error', 'Error deleting blog post: ' . $e->getMessage());
        }
    }
}








