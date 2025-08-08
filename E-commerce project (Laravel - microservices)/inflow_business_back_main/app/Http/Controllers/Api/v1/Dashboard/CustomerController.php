<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Events\CustomerCreatedEvent;
use App\Filters\CustomerFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\CustomerCreateRequest;
use App\Http\Requests\v1\Dashboard\CustomerUpdateRequest;
use App\Http\Resources\v1\Dashboard\CustomerCollection;
use App\Http\Resources\v1\Dashboard\CustomerDetailResource;
use App\Http\Resources\v1\Dashboard\CustomerForDropoutResource;
use App\Http\Resources\v1\Dashboard\CustomerResource;
use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected Tenant $tenant;

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
    }

    public function index(CustomerFilter $filter, Request $request)
    {
        $customers = Customer::filter($filter)->paginate($request->per_page ?? 15);

        return api_response(new CustomerCollection($customers));
    }

    public function dropout(CustomerFilter $filter, Request $request)
    {
        $customers = Customer::filter($filter)->paginate($request->per_page ?? 15);

        return api_response(CustomerForDropoutResource::collection($customers));
    }

    public function store(CustomerCreateRequest $request)
    {
        $input = $request->validated();
        if (isset($input['password'])) {
            $input['password'] = bcrypt($input['password']);
        }
        $input['email_verified_at'] = now();
        $input['phone_verified_at'] = now();
        /** @var Customer $customer */
        $customer = Customer::create($input);
        event(new CustomerCreatedEvent($customer));

        return api_response(new CustomerDetailResource($customer->refresh()));
    }

    public function show($id)
    {
        /** @var Customer $customer */
        $customer = Customer::findOrFail($id);

        return api_response(new CustomerDetailResource($customer));
    }

    public function update($id, CustomerUpdateRequest $request)
    {
        /** @var Customer $customer */
        $customer = Customer::findOrFail($id);

        $customer->update($request->validated());

        return api_response(new CustomerDetailResource($customer));
    }

    public function addBonuses($id, Request $request, LoyaltyService $loyaltyService)
    {
        /** @var Customer $customer */
        $customer = Customer::findOrFail($id);

        $response = $loyaltyService->addBonusesToCustomer($id, $request);

        if ($response->status() != 200) {
            return api_response($response->json(), $response->status());
        }

        return api_response(new CustomerDetailResource($customer->refresh()));
    }
}
