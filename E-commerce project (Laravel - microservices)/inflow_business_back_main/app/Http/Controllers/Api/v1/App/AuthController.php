<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Enums\AuthTypes;
use App\Events\CustomerCreatedEvent;
use App\Http\Requests\v1\App\EmailConfirmationRequest;
use App\Http\Requests\v1\App\EmailLoginRequest;
use App\Http\Requests\v1\App\EmailRegisterRequest;
use App\Http\Requests\v1\App\FillAccountRequest;
use App\Http\Requests\v1\App\PasswordResetRequest;
use App\Http\Requests\v1\App\PhoneConfirmationRequest;
use App\Http\Requests\v1\App\PhoneLoginRequest;
use App\Http\Requests\v1\App\ResendConfirmationCodeToEmailRequest;
use App\Http\Requests\v1\App\ResendConfirmationCodeToPhoneRequest;
use App\Http\Resources\v1\App\CustomerResource;
use App\Jobs\SendNewPasswordJob;
use App\Models\AppSetting;
use App\Models\ConfirmationCode;
use App\Models\Customer;
use App\Services\Database\CustomerService;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AuthController extends AppController
{
    use HelperTrait;

    protected AppSetting $settings;

    public function __construct(protected CustomerService $customerService)
    {
        parent::__construct();
        $this->settings = AppSetting::first();
    }

    public function emailRegister(EmailRegisterRequest $request)
    {
        DB::beginTransaction();
        /** @var Customer $customer */
        $customer = Customer::updateOrCreate([
            'email' => $request->email
        ], $request->validated());
        if ($request->password) {
            $customer->update(['password' => bcrypt($request->password)]);
        }
        $this->customerService->sendConfirmationCode($customer, AuthTypes::Email);
        DB::commit();

        if ($customer->wasRecentlyCreated) {
            event(new CustomerCreatedEvent($customer));
        }

        return api_response(['message' => 'Код отправлен на вашу почту']);
    }

    public function confirmEmail(EmailConfirmationRequest $request)
    {
        $confirmation_code = ConfirmationCode::whereCode($request->code)
            ->whereType(AuthTypes::Email)
            ->whereValue($request->email)
            ->first();
        if (!$confirmation_code) {
            return api_error('Код указан неверно или устарел');
        }
        /** @var Customer $customer */
        $customer = Customer::find($confirmation_code->customer_id);
        if (!$customer) {
            return api_error('Пользователь не найден');
        }
        $confirmation_code->delete();
        if (!$customer->email_verified_at) {
            $customer->update(['email_verified_at' => now()]);
        }
        $token = auth('customer')->login($customer);

        return $this->respondWithToken($token);
    }

    public function resendConfirmationEmail(ResendConfirmationCodeToEmailRequest $request)
    {
        $customer = Customer::whereEmail($request->email)->first();
        $confirmation_code = ConfirmationCode::whereType(AuthTypes::Email)
            ->whereValue($request->email)
            ->first();

        if ($confirmation_code && Carbon::parse($confirmation_code->created_at)->diffInSeconds(now()) < config('app.email_cooldown')) {
            $diff = floor(config('app.email_cooldown') - Carbon::parse($confirmation_code->created_at)->diffInSeconds(now()));
            $word = $this->endingByNumber($diff, ['секунду', 'секунды', 'секунд']);

            return api_error("Запросить код повторно можно только через $diff $word");
        }

        ConfirmationCode::whereCode($request->code)
            ->whereType(AuthTypes::Email)
            ->whereValue($request->email)
            ->delete();

        $this->customerService->sendConfirmationCode($customer, AuthTypes::Email);

        return api_response(['message' => 'Код отправлен на вашу почту']);
    }

    public function emailLogin(EmailLoginRequest $request)
    {
        $auth = auth('customer')->attempt(['email' => $request->email, 'password' => $request->password]);
        if (!$auth) {
            return api_error('Неверные логин или пароль');
        }
        $customer = Customer::whereEmail($request->email)->first();
        if (!$customer->email_verified_at) {
            return api_error('Почта не подтверждена');
        }

        if (!$this->settings->two_factor) {
            return $this->respondWithToken($auth);
        }

        $this->customerService->sendConfirmationCode($customer, AuthTypes::Email);

        return api_response(['message' => 'Код отправлен на вашу почту']);
    }

    public function phoneLogin(PhoneLoginRequest $request)
    {
        $customer = Customer::firstOrCreate([
            'phone' => $request->phone
        ]);

        if ($customer->wasRecentlyCreated) {
            event(new CustomerCreatedEvent($customer));
        }

        $this->customerService->sendConfirmationCode($customer, AuthTypes::Phone);


        return api_response(['message' => 'Код отправлен на ваш номер телефона']);
    }

    public function confirmPhone(PhoneConfirmationRequest $request)
    {
        if (!auth()->check() || auth('customer')->user() == $request->phone) {
            $confirmation_code = ConfirmationCode::whereCode($request->code)
                ->whereType(AuthTypes::Phone)
                ->whereValue($request->phone)
                ->first();
            if (!$confirmation_code) {
                return api_error('Код указан неверно или устарел');
            }
            $customer = Customer::find($confirmation_code->customer_id);
            if (!$customer) {
                return api_error('Пользователь не найден');
            }
            $confirmation_code->delete();
        } else {
            /** @var Customer $customer */
            $customer = auth('customer')->user();
            $result = $this->customerService->updatePhone($customer, $request->code);
            if (!$result) {
                return api_error('Код указан неверно или устарел');
            }
        }

        if (!$customer->phone_verified_at) {
            $customer->update([
                'phone_verified_at' => now()
            ]);
        }
        $token = auth('customer')->login($customer);

        return $this->respondWithToken($token);
    }

    public function fillAccount(FillAccountRequest $request)
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        $customer->update(['name' => $request->name]);
        if ($request->phone) {
            if ($this->settings->auth_type == AuthTypes::Email) {
                $customer->phone = $request->phone;
                $customer->phone_verified_at = now();
            } else {
                $this->customerService->initPhoneUpdate($customer, $request->phone);
            }
        }
        if ($request->email) {
            $this->customerService->initEmailUpdate($customer, $request->email);
        }
        $customer->update();

        return api_response(new CustomerResource($customer->refresh()));
    }

    public function resendConfirmationSms(ResendConfirmationCodeToPhoneRequest $request)
    {
        $customer = Customer::wherePhone($request->phone)->first();
        $confirmation_code = ConfirmationCode::whereType(AuthTypes::Phone)
            ->whereValue($request->phone)
            ->first();

        if ($confirmation_code && Carbon::parse($confirmation_code->created_at)->diffInSeconds(now()) < config('app.sms_cooldown')) {
            $diff = floor(config('app.sms_cooldown') - Carbon::parse($confirmation_code->created_at)->diffInSeconds(now()));
            $word = $this->endingByNumber($diff, ['секунду', 'секунды', 'секунд']);

            return api_error("Запросить код повторно можно только через $diff $word");
        }

        ConfirmationCode::whereCode($request->code)
            ->whereType(AuthTypes::Phone)
            ->whereValue($request->phone)
            ->delete();

        $this->customerService->sendConfirmationCode($customer, AuthTypes::Phone);

        return api_response(['message' => 'Код отправлен на ваш номер телефона']);
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        $customer = Customer::whereEmail($request->input('email'))->first();
        $new_password = Str::password(length: 8, symbols: false);
        $customer->update(['password' => bcrypt($new_password)]);

        SendNewPasswordJob::dispatch($customer, $new_password);

        return api_response(['message' => 'На указанный e-mail отправлен новый пароль']);
    }

    public function logout()
    {
        auth('customer')->user()->devices()->delete();
        auth('customer')->invalidate(true);

        return api_response(['message' => 'Вы вышли']);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('customer')->refresh());
    }

    public function switchAuthType(Request $request)
    {
        $this->settings->update(['auth_type' => $request->auth_type]);
    }

    protected function respondWithToken($token)
    {
        return api_response([
            'token' => [
                'access_token' => $token,
                'refresh_token' => $token,
            ],
            'token_type' => 'bearer',
            'expires_in' => auth('customer')->factory()->getTTL() * 60,
            'user' => new CustomerResource(auth('customer')->user()),
        ]);
    }
}
