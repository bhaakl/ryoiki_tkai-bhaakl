<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Contracts\PaymentGateContract;
use App\Enums\FfdVersions;
use App\Enums\PaymentGates;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\AcquiringUpdateRequest;
use App\Http\Resources\v1\Dashboard\Acquiring\AcquiringDetailResource;
use App\Models\Acquiring;
use Illuminate\Http\Request;

class AcquiringController extends Controller
{
    public function __construct(protected PaymentGateContract $paymentGate)
    {
    }

    public function show()
    {
        $acquiring = Acquiring::whereName($this->paymentGate->getGateName())->first();
        if (!$acquiring) {
            $keys = $this->paymentGate->keys;
            $default_keys = [];
            foreach ($keys as $key) {
                $default_keys[$key] = '';
            }
            $acquiring = Acquiring::create([
                'name' => $this->paymentGate->getGateName(),
                'keys' => $default_keys
            ]);
        }

        return new AcquiringDetailResource($acquiring);
    }

    public function update($id, AcquiringUpdateRequest $request)
    {
        /** @var Acquiring $acquiring */
        $acquiring = Acquiring::findOrFail($id);

        $acquiring->ffd = $request->ffd;
        $gate = new  ($acquiring->name->gate());
        $keys = [];
        foreach ($gate->keys as $key) {
            $keys[$key] = encrypt($request->$key);
        }
        $acquiring->keys = $keys;
        if ($request->ffd) {
            $ffd = FfdVersions::from($request->ffd);
            $ffd = new ($ffd->FiscalizationClass());
            $ffd_keys = [];
            foreach ($ffd->required_fields as $key => $val) {
                $ffd_keys[$key] = $request->$key;
            }
            $acquiring->ffd_keys = $ffd_keys;
        } else {
            $acquiring->ffd_keys = null;
        }

        $acquiring->update();

        return new AcquiringDetailResource($acquiring->refresh());
    }
}
