<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UploadApiController extends Controller
{
    private function getModel($model)
    {
        $className = 'App\\Models\\' . Str::studly(Str::singular($model));
        if (class_exists($className)) return new $className;
        abort(404, 'Model not found');
    }

    private function getFolder($model)
    {
        return Str::plural(Str::lower($model));
    }

    public function store(Request $request, $model)
    {
        $request->validate([
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        $data = $request->except(['image', '_method']);

        // Parse JSON fields (arrays)
        foreach ($data as $key => $value) {
            if (is_string($value) && (Str::startsWith($value, '[') || Str::startsWith($value, '{'))) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) $data[$key] = $decoded;
            }
        }

        if ($request->hasFile('image')) {
            $folder = $this->getFolder($model);
            $path = $request->file('image')->store($folder, 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        if ($model === 'news' && empty($data['excerpt']) && !empty($data['content'])) {
            $clean = preg_replace('/\s+/', ' ', trim(strip_tags(html_entity_decode($data['content'], ENT_QUOTES, 'UTF-8'))));
            $data['excerpt'] = Str::limit($clean, 150);
        }

        $instance = $this->getModel($model)->create($data);
        return response()->json($instance, 201);
    }

    public function update(Request $request, $model, $id)
    {
        $request->validate([
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
        ]);

        $instance = $this->getModel($model)->findOrFail($id);
        $data = $request->except(['image', '_method']);

        // Parse JSON fields (arrays)
        foreach ($data as $key => $value) {
            if (is_string($value) && (Str::startsWith($value, '[') || Str::startsWith($value, '{'))) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) $data[$key] = $decoded;
            }
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($instance->image_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $instance->image_url));
            }
            $folder = $this->getFolder($model);
            $path = $request->file('image')->store($folder, 'public');
            $data['image_url'] = '/storage/' . $path;
        }

        if ($model === 'news' && empty($data['excerpt']) && !empty($data['content'])) {
            $clean = preg_replace('/\s+/', ' ', trim(strip_tags(html_entity_decode($data['content'], ENT_QUOTES, 'UTF-8'))));
            $data['excerpt'] = Str::limit($clean, 150);
        }

        $instance->update($data);
        return response()->json($instance);
    }

    public function destroy($model, $id)
    {
        $instance = $this->getModel($model)->findOrFail($id);
        if ($instance->image_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $instance->image_url));
        }
        $instance->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
