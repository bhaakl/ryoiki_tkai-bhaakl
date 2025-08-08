<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Отображение запроса ссылки на сброс пароля.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Обработка входящего запроса на ссылку для сброса пароля.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Мы отправим этому пользователю ссылку для сброса пароля.
        // После того как мы попытаемся отправить ссылку, мы изучим ответ
        // и увидим, какое сообщение нужно показать пользователю. Наконец, мы отправим правильный ответ.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
