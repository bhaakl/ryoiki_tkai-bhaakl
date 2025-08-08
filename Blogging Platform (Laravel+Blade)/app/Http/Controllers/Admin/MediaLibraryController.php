<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaLibraryRequest;
use App\Models\Media;
use App\Models\MediaLibrary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaLibraryController extends Controller
{
    /**
     * Вернуть медиатеку.
     */
    public function index(Request $request): View
    {
        return view('admin.media.index', [
            'media' => MediaLibrary::first()->media()->get()
        ]);
    }

    /**
     * Отображение указанного ресурса.
     */
    public function show(Media $medium): Media
    {
        return $medium;
    }

    /**
     * Отображение формы для создания нового ресурса.
     */
    public function create(Request $request): View
    {
        return view('admin.media.create');
    }

    /**
     * Сохранение только что созданного ресурса в хранилище.
     */
    public function store(MediaLibraryRequest $request): RedirectResponse
    {
        $image = $request->file('image');
        $name = $image->getClientOriginalName();

        if ($request->filled('name')) {
            $name = $request->input('name');
        }

        MediaLibrary::first()
            ->addMedia($image)
            ->usingName($name)
            ->toMediaCollection();

        return redirect()->route('admin.media.index')->withSuccess(__('media.created'));
    }

    /**
     * Удаление указанного ресурса из хранилища.
     */
    public function destroy(Media $medium): RedirectResponse
    {
        $medium->delete();

        return redirect()->route('admin.media.index')->withSuccess(__('media.deleted'));
    }
}
