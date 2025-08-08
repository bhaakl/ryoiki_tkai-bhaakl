<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Enums\AboutTemplates;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\AboutImageUploadRequest;
use App\Http\Requests\v1\Dashboard\AboutItemCreateRequest;
use App\Http\Requests\v1\Dashboard\AboutItemUpdateRequest;
use App\Http\Requests\v1\Dashboard\AboutMainImageUploadRequest;
use App\Http\Resources\v1\Dashboard\AboutItemDetailResource;
use App\Http\Resources\v1\Dashboard\AboutItemResource;
use App\Http\Resources\v1\Dashboard\AboutTemplateResource;
use App\Http\Resources\v1\Dashboard\MediaResource;
use App\Models\About;
use App\Models\AboutItem;
use App\Models\Media;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    use ImageTrait;

    protected Tenant $tenant;

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
        About::firstOrCreate();
    }

    public function templates()
    {
        $templates = AboutTemplates::cases();

        return api_response(AboutTemplateResource::collection($templates));
    }

    public function index()
    {
        $items = AboutItem::all();

        return api_response(AboutItemResource::collection($items));
    }

    public function show($id, Request $request)
    {
        $item = AboutItem::findOrFail($id);

        return api_response(new AboutItemDetailResource($item));
    }

    public function store(AboutItemCreateRequest $request)
    {
        $item = AboutItem::create($request->validated());

        return api_response(new AboutItemDetailResource($item));
    }

    public function update($id, AboutItemUpdateRequest $request)
    {
        /** @var AboutItem $item */
        $item = AboutItem::findOrFail($id);

        $item->update($request->validated());

        if ($item->type == AboutTemplates::license) {
            $media = Media::whereUuid($request->uuid)->first();
            if ($media) {
                if ($request->name && $media->hasCustomProperty('name')) {
                    $media->setCustomProperty('name', $request->name);
                }
                if ($request->description && $media->hasCustomProperty('description')) {
                    $media->setCustomProperty('description', $request->description);
                }
                $media->update();
            }
        }

        return api_response(new AboutItemDetailResource($item));
    }

    public function uploadImage($id, AboutImageUploadRequest $request)
    {
        /** @var AboutItem $item */
        $item = AboutItem::findOrFail($id);

        if ($item->type == AboutTemplates::photo && $request->file('images')) {
            foreach ($request->file('images') as $image) {
                $this->uploadPhoto($image, $item);
            }
        }
        if ($item->type == AboutTemplates::license && $request->file('image')) {
            $this->uploadPhoto(
                photo: $request->file('image'),
                model: $item,
                properties: [
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                ]);
        }

        return api_response(new AboutItemDetailResource($item));
    }

    public function getMainImage()
    {
        $about = About::firstOrCreate();

        return api_response(new MediaResource($about->getFirstMedia()));
    }

    public function uploadMainImage(AboutMainImageUploadRequest $request)
    {
        $about = About::firstOrCreate();
        $this->uploadPhoto(
            photo: $request->file('image'),
            model: $about
        );

        return api_response(new MediaResource($about->getFirstMedia()));
    }

    public function destroy($id)
    {
        /** @var AboutItem $item */
        $item = AboutItem::findOrFail($id);

        $item->delete();

        return api_response(['message' => 'ok']);
    }
}
