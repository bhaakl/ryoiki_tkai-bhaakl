<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Enums\AuthTypes;
use App\Http\Requests\v1\App\AddDeviceRequest;
use App\Http\Requests\v1\App\BirthdayUpdateRequest;
use App\Http\Requests\v1\App\ConfirmationCodeRequest;
use App\Http\Requests\v1\App\EmailUpdateRequest;
use App\Http\Requests\v1\App\NameUpdateRequest;
use App\Http\Requests\v1\App\PasswordUpdateRequest;
use App\Http\Requests\v1\App\PhoneUpdateRequest;
use App\Http\Requests\v1\App\UpdatePushSettingRequest;
use App\Http\Resources\v1\App\CustomerResource;
use App\Models\AppSetting;
use App\Models\Device;
use App\Services\Database\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends AppController
{
    protected AppSetting $settings;

    public function __construct(protected CustomerService $customerService)
    {
        parent::__construct();
        $this->settings = AppSetting::first();
    }

    public function me()
    {
        return api_response(new CustomerResource(auth('customer')->user()));
    }

    public function updateName(NameUpdateRequest $request)
    {
        auth('customer')->user()->update($request->validated());

        return api_response(new CustomerResource(auth('customer')->user()->refresh()));
    }

    public function updateBirthday(BirthdayUpdateRequest $request)
    {
        auth('customer')->user()->update($request->validated());

        return api_response(new CustomerResource(auth('customer')->user()->refresh()));
    }

    public function updatePassword(PasswordUpdateRequest $request)
    {
        if (!Hash::check($request->input('old_password'), auth('customer')->user()->password)) {
            throw ValidationException::withMessages(['old_password' => 'Старый пароль неверный']);
        }
        auth('customer')->user()->update(['password' => bcrypt($request->input('password'))]);

        return api_response(new CustomerResource(auth('customer')->user()));
    }

    public function updateEmail(EmailUpdateRequest $request)
    {
        $customer_service = new CustomerService();
        $customer_service->initEmailUpdate(auth('customer')->user(), $request->input('email'));

        return api_response(['message' => 'Код отправлен на вашу почту']);
    }

    public function confirmEmailUpdate(ConfirmationCodeRequest $request)
    {
        $customer_service = new CustomerService();
        $result = $customer_service->updateEmail(auth('customer')->user(), $request->input('code'));

        return $result ? api_response(auth('customer')->user()->refresh()) : api_error('Код указан неверно или устарел');
    }

    public function updatePhone(PhoneUpdateRequest $request)
    {
        if ($this->settings->auth_type == AuthTypes::Email) {
            auth('customer')->user()->update(['phone' => $request->input('phone')]);

            return api_response(auth('customer')->user()->refresh());
        } else {
            $customer_service = new CustomerService();
            $customer_service->initPhoneUpdate(auth('customer')->user(), $request->input('phone'));

            return api_response(['message' => 'Код отправлен на ваш телефон']);
        }
    }

    public function confirmPhoneUpdate(ConfirmationCodeRequest $request)
    {
        $customer_service = new CustomerService();
        $result = $customer_service->updatePhone(auth('customer')->user(), $request->input('code'));

        return $result ? api_response(auth('customer')->user()->refresh()) : api_error('Код указан неверно или устарел');
    }

    public function updatePushSettings(UpdatePushSettingRequest $request)
    {
        auth('customer')->user()->update($request->validated());

        return api_response(new CustomerResource(auth('customer')->user()->refresh()));
    }

    public function addDevice(AddDeviceRequest $request)
    {
        Device::firstOrCreate([
            'customer_id' => auth('customer')->id(),
            'type' => $request->type,
            'token' => $request->token,
        ]);

        return api_response(['message' => 'ok']);
    }

    public function destroy(Request $request)
    {
        if ($request->force) {
            auth('customer')->user()->forceDelete();
        } else {
            auth('customer')->user()->delete();
        }

        return api_response(['message' => 'Профиль удалён']);
    }
}
