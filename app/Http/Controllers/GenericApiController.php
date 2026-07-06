<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GenericApiController extends Controller
{
    private function getModel($model)
    {
        $className = 'App\\Models\\' . Str::studly(Str::singular($model));
        if (class_exists($className)) {
            return new $className;
        }
        abort(404, 'Model not found');
    }

    public function index($model)
    {
        return response()->json($this->getModel($model)->all());
    }

    public function show($model, $id)
    {
        return response()->json($this->getModel($model)->findOrFail($id));
    }

    public function store(Request $request, $model)
    {
        $instance = $this->getModel($model)->create($request->all());
        return response()->json($instance, 201);
    }

    public function update(Request $request, $model, $id)
    {
        $instance = $this->getModel($model)->findOrFail($id);
        $instance->update($request->all());
        return response()->json($instance);
    }

    public function destroy($model, $id)
    {
        $instance = $this->getModel($model)->findOrFail($id);
        $instance->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
