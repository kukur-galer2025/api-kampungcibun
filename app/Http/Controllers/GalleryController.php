<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        return response()->json(Gallery::all());
    }

    public function show($id)
    {
        return response()->json(Gallery::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'color' => 'nullable|string',
            'height' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'category', 'color', 'height']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('galleries', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $gallery = Gallery::create($data);
        return response()->json($gallery, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:255',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'color' => 'nullable|string',
            'height' => 'nullable|string',
        ]);

        $gallery = Gallery::findOrFail($id);
        $data = $request->only(['title', 'category', 'color', 'height']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($gallery->image_url) {
                $oldPath = str_replace('/storage/', '', $gallery->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('galleries', 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        $gallery->update($data);
        return response()->json($gallery);
    }

    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        if ($gallery->image_url) {
            $oldPath = str_replace('/storage/', '', $gallery->image_url);
            Storage::disk('public')->delete($oldPath);
        }
        $gallery->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
