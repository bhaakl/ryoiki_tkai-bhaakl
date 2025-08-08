<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\PropertyEnumRequest;
use App\Http\Requests\v1\PropertyStoreRequest;
use App\Http\Requests\v1\PropertyUpdateRequest;
use App\Http\Resources\v1\Dashboard\Property\PropertyCollection;
use App\Http\Resources\v1\Dashboard\Property\PropertyEnumResource;
use App\Http\Resources\v1\Dashboard\Property\PropertyResource;
use App\Models\Property;
use App\Models\PropertyEnum;
use App\Models\PropertyString;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::orderBy('name')->paginate($request->per_page ?? self::PER_PAGE);

        return new PropertyCollection($properties);
    }

    public function dropout(Request $request)
    {
        $properties = Property::orderBy('name')->get();

        return PropertyResource::collection($properties);
    }

    public function store(PropertyStoreRequest $request)
    {
        $property = Property::create($request->validated());

        return new PropertyResource($property);
    }

    public function show($id)
    {
        /** @var Property $property */
        $property = Property::findOrFail($id);

        return new PropertyResource($property);
    }

    public function update($id, PropertyUpdateRequest $request)
    {
        /** @var Property $property */
        $property = Property::findOrFail($id);
        $property->update($request->validated());

        return new PropertyResource($property);
    }

    public function destroy($id)
    {
        /** @var Property $property */
        $property = Property::findOrFail($id);
        $property->delete();

        return response()->json(['message' => 'ok']);
    }

    public function enumList($id)
    {
        /** @var Property $property */
        $property = Property::findOrFail($id);

        $enums = $property->property_enums()->orderBy('value')->get();

        return PropertyEnumResource::collection($enums);
    }

    public function addEnum(PropertyEnumRequest $request)
    {
        $enum = PropertyEnum::create($request->validated());

        return new PropertyEnumResource($enum);
    }

    public function destroyEnum($id)
    {
        $enum = PropertyEnum::findOrFail($id);
        $enum->delete();

        return response()->json(['message' => 'ok']);
    }

    public function getStrings(Request $request)
    {
        $strings = PropertyString::query();
        if ($request->search) {
            $strings = $strings->where('value', 'like', "%$request->search%");
        }
        $strings = $strings->orderBy('name')->distinct()->get(['name']);

        return $strings;
    }
}
