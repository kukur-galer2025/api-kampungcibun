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

    /**
     * Strip ALL HTML (tags + entities) from rich text editor content.
     * Handles: <strong>, <em>, <b>, <i>, <u>, <s>, <br>, <p>, <div>,
     * <h1>-<h6>, <ul>, <ol>, <li>, <blockquote>, <a>, <span>, etc.
     * Also decodes &nbsp;, &amp;, &lt;, &gt;, &quot; and all other entities.
     */
    private function cleanHtml(?string $html): string
    {
        if (!$html) return '';

        // 1. Decode all HTML entities (&nbsp; -> space, &amp; -> &, etc.)
        $text = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 2. Insert space before closing block-level tags so words don't merge
        //    e.g. "<p>Hello</p><p>World</p>" -> "Hello World" instead of "HelloWorld"
        $text = preg_replace('/<\/(p|div|br|h[1-6]|li|blockquote|tr|td|th)\s*>/i', ' ', $text);

        // 3. Replace <br>, <br/>, <br /> with space
        $text = preg_replace('/<br\s*\/?>/i', ' ', $text);

        // 4. Strip ALL remaining HTML tags (<strong>, <em>, <a>, <span>, etc.)
        $text = strip_tags($text);

        // 5. Normalize whitespace (multiple spaces/tabs/newlines -> single space)
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
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
            $data['excerpt'] = Str::limit($this->cleanHtml($data['content']), 150);
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

        if ($model === 'news') {
            if (!empty($data['content'])) {
                $data['excerpt'] = Str::limit($this->cleanHtml($data['content']), 150);
            } else {
                $data['excerpt'] = '';
            }
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
