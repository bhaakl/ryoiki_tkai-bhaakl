<?php

namespace App\Http\Requests\v1;

use App\Contracts\PaymentGateContract;
use App\Enums\FfdVersions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AcquiringUpdateRequest extends FormRequest
{
    public function __construct(protected PaymentGateContract $paymentGate, array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'ffd' => [
                'sometimes',
                Rule::in(FfdVersions::cases())
            ]
        ];
        $gate = $this->paymentGate;
        $keys = $gate->keys;
        $add_rules = [];
        foreach ($keys as $key) {
            $add_rules[$key] = 'required';
        }
        $rules = array_merge($rules, $add_rules);
        if (request()->ffd && FfdVersions::tryFrom(request()->ffd)) {
            $version = FfdVersions::from(request()->ffd);
            $ffd = new ($version->FiscalizationClass());
            $rules = array_merge($rules, $ffd->required_fields);
        }

        return $rules;
    }
}
