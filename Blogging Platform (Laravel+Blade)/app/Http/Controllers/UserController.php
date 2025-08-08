<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersRequest;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\MediaFile;



class UserController extends Controller
{
    /**
     * Отображение определенного ресурса.
     */
    public function show(Request $request, User $user): View
    {
        return view('users.show', [
            'user' => $user,
            'posts_count' => $user->posts()->count(),
            'posts' => $user->posts()->latest()->limit(5)->get(),
        ]);
    }

    /**
     * Отображение формы для редактирования определенного ресурса.
     */
    public function edit(): View
    {
        $user = auth()->user();

        $this->authorize('update', $user);

        return view('users.edit', [
            'user' => $user,
            'roles' => Role::all()
        ]);
    }

   /**
 * Обновление определенного ресурса в базе данных.
 */
    public function update(UsersRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $this->authorize('update', $user);

        if ($request->hasFile('profile_img')) {
            $image = $request->file('profile_img');
            
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();

            $image->storeAs('public/profiles', $filename);

            $media = new MediaFile();
            $media->filename = $filename;
            $media->original_filename = $image->getClientOriginalName();
            $media->mime_type = $image->getClientMimeType();
            $media->save();

            $user->profile_img_id = $media->id;
        }

        $user->update($request->validated());

        return redirect()->route('users.edit')->withSuccess(__('users.updated'));
    }

}
