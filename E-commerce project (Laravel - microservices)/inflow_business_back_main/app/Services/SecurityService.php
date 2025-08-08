<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Jobs\SendConfirmationCodeToSmsJob;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SecurityService
{
    private ?User $user;

    private const CONFIRMATION_CODE_TTL_SECONDS = 120;
    private const PASSWORD_RESET_TOKEN_TTL_MINUTES = 60;

    public function __construct(?User $user)
    {
        $this->user = $user;
    }

    public function confirmPhone(string $code): void
    {
        $cachedCode = Cache::store('landlord')->get($this->getConfirmationCodeKey($this->user->phone));
        if ($cachedCode !== $code) {
            throw new ServiceException('Код неверный.');
        }

        if (!$this->user->phone_verified_at) {
            $this->user->phone_verified_at = now();
            $this->user->save();
        }

        Cache::store('landlord')->delete($this->getConfirmationCodeKey($this->user->phone));
    }

    public function sendCode(): void
    {
        if (Cache::store('landlord')->get($this->getConfirmationCodeKey($this->user->phone)) && app()->isProduction()) {
            throw new ServiceException('Запросить новый код можно через 2 минуты');
        }

        $code = mt_rand(10000, 99999);
        if (!app()->isProduction()) {
            $code = 11111;
        }
        Cache::store('landlord')->put(
            $this->getConfirmationCodeKey($this->user->phone),
            (string) $code,
            now()->addSeconds(self::CONFIRMATION_CODE_TTL_SECONDS),
        );
        SendConfirmationCodeToSmsJob::dispatch($this->user->phone, $code);
    }

    public function sendCodeToAnotherNumber($old_phone): void
    {
        if (Cache::store('landlord')->get($this->getConfirmationCodeKey($old_phone)) && app()->isProduction()) {
            throw new ServiceException('Запросить новый код можно через 2 минуты');
        }

        $code = mt_rand(10000, 99999);
        if (!app()->isProduction()) {
            $code = 11111;
        }
        Cache::store('landlord')->put(
            $this->getConfirmationCodeKey($this->user->phone),
            (string) $code,
            now()->addSeconds(self::CONFIRMATION_CODE_TTL_SECONDS),
        );
        SendConfirmationCodeToSmsJob::dispatch($this->user->phone, $code);
    }

    public function sendResetLink(User $user): void
    {
        if (PasswordResetToken::whereEmail($user->email)->first()) {
            throw new ServiceException('Ссылка уже была отправлена');
        }
        $token = Str::random(255);
        while (PasswordResetToken::where('token', $token)->exists()) {
            $token = Str::random(255);
        }
        $url = config('app.frontend_url') . '/login/update?token=' . $token;
        PasswordResetToken::make([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ])->save();
        // TODO: email with link to form to reset password
//        Mail::to($user->email)->send(new ResetPasswordLinkMail($url));
    }

    public function resetPassword(string $newPassword, string $token): void
    {
        $cachedToken = PasswordResetToken::whereToken($token)->first();
        if (empty($cachedToken) || $cachedToken->token !== $token) {
            throw new ServiceException('Ошибка сброса пароля');
        }
        $user = User::email($cachedToken->getAttributes()['email'])->first();
        if (!$user || $cachedToken->created_at->diffInMinutes(now()) >= self::PASSWORD_RESET_TOKEN_TTL_MINUTES) {
            throw new ServiceException('Ошибка сброса пароля');
        }
        DB::beginTransaction();
        $user->update(['password' => Hash::make($newPassword)]);
        $cachedToken->delete();
        DB::commit();
    }

    private function getConfirmationCodeKey(string $phone): string
    {
        return sprintf('confirmation_code:%s', $phone);
    }
}
