<?php

namespace App\Http\Controllers\Api\v1\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Dashboard\ChiefCollection;
use App\Models\User;
use App\Services\ChiefService;
use Illuminate\Http\Request;

class ChiefController extends Controller
{
    public function __construct(protected ChiefService $chiefService) {}

    public function chiefs(Request $request)
    {
        $perPage = $request->per_page;
        $search = $request->search;

        $query = User::role('chief')->with('tenant');

        if (isset($search)) {
            $chiefs = User::search($search)->get();
            // $query->where(function ($q) use ($search) {
            //     $q->where('name', 'like', "%{$search}%")
            //         ->orWhereHas('tenant', function ($subQuery) use ($search) {
            //             $subQuery->where('name', 'like', "%{$search}%");
            //         });
            // });
        }

        $chiefs = $chiefs->paginate($perPage ?? self::PER_PAGE);

       return ChiefCollection::make($chiefs);
    }

    public function chief(Request $request)
    {
        return api_response($this->chiefService->chief());

    }
}