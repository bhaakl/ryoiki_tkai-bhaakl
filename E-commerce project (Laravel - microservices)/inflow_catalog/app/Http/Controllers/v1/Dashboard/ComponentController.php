<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Enums\MeasurementUnits;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\ComponentCreateRequest;
use App\Http\Resources\v1\ComponentResource;
use App\Http\Resources\v1\MeasurementUnitResource;
use App\Models\Component;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function index(Request $request)
    {
        $components = Component::query();
        if ($request->has('search')) {
            $components = $components->where('name', 'like', "%{$request->get('search')}%");
        }
        $components = $components->orderBy('name')->get();

        return response()->json(ComponentResource::collection($components));
    }

    public function units()
    {
        $units = MeasurementUnits::cases();

        return response()->json(MeasurementUnitResource::collection($units));
    }

    public function store(ComponentCreateRequest $request)
    {
        $component = Component::create($request->validated());

        return response()->json(new ComponentResource($component));
    }

    public function update(Component $component, ComponentCreateRequest $request)
    {
        $component->update($request->validated());

        return response()->json(new ComponentResource($component));
    }

    public function destroy(Component $component)
    {
        $component->delete();

        return response()->json(['message' => 'ok']);
    }
}
