<?php

namespace App\Services\Database;

use App\Enums\AuthTypes;
use App\Jobs\SendConfirmationCodeToEmailJob;
use App\Jobs\SendConfirmationCodeToSmsJob;
use App\Models\ConfirmationCode;
use App\Models\Customer;
use App\Models\EmailChange;
use App\Models\PhoneChange;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    public function sendConfirmationCode(Customer $customer, $type): void
    {
        $code = app()->isProduction() ? mt_rand(1000, 9999) : 1111;
        $confirmation = ConfirmationCode::firstOrCreate([
            'customer_id' => $customer->id,
            'type' => $type,
            'value' => $type == AuthTypes::Email ? $customer->email : $customer->phone,
            'code' => $code
        ]);
        if (!$confirmation->wasRecentlyCreated) {
            $confirmation->update(['created_at' => now()]);
        }

        if ($type == AuthTypes::Email) {
            Log::debug('SendConfirmationCodeToEmailJob dispatch');
            SendConfirmationCodeToEmailJob::dispatch($customer, $code);
        } else {
            SendConfirmationCodeToSmsJob::dispatch($customer->phone, $code);
        }
    }

    public function initEmailUpdate(Customer $customer, $email): void
    {
        $code = app()->isProduction() ? mt_rand(1000, 9999) : 1111;
        EmailChange::whereCustomerId($customer->id)->delete();
        EmailChange::create([
            'customer_id' => $customer->id,
            'email' => $email,
            'code' => $code
        ]);
        SendConfirmationCodeToEmailJob::dispatch($customer, $code);
    }

    public function updateEmail(Customer $customer, $code): bool
    {
        $new_email = EmailChange::whereCustomerId($customer->id)->whereCode($code)->first();
        if (!$new_email || Customer::whereEmail($new_email->email)->first()) {
            return false;
        }
        $customer->update(['email' => $new_email->email]);
        $new_email->delete();

        return true;
    }

    public function initPhoneUpdate(Customer $customer, $phone): void
    {
        Log::channel('requests')->debug('initPhoneUpdate', [
            'customer' => $customer->toArray(),
            'phone' => $phone
        ]);
        $code = app()->isProduction() ? mt_rand(1000, 9999) : 1111;
        PhoneChange::whereCustomerId($customer->id)->delete();
        PhoneChange::create([
            'customer_id' => $customer->id,
            'phone' => $phone,
            'code' => $code
        ]);
        SendConfirmationCodeToSmsJob::dispatch($customer->phone, $code);
    }

    public function updatePhone(Customer $customer, $code): bool
    {
        Log::debug('updatePhone', [
            'customer' => $customer->toArray(),
            'code' => $code
        ]);
        $new_phone = PhoneChange::whereCustomerId($customer->id)->whereCode($code)->first();
        Log::debug('updatePhone', [
            '$new_phone' => $new_phone?->toArray(),
        ]);
        if (!$new_phone || Customer::wherePhone($new_phone->phone)->first()) {
            return false;
        }
        $customer->update(['phone' => $new_phone->phone]);
        $new_phone->delete();

        return true;
    }
}
