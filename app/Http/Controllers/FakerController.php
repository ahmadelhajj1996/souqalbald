<?php

namespace App\Http\Controllers;

use App\Models\JobAd;
use App\Models\Product;
use App\Models\Service;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FakerController extends Controller
{
    use ApiResponseTrait;

    public function createFake(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:job,service,product'],
        ]);

        return match ($request->type) {
            'product' => $this->fakeProduct($request),
            'service' => $this->fakeService($request),
            'job' => $this->fakeJob($request),
        };
    }

    public function fakeProduct(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:100'],
            'lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
        ]);

        $product = Product::factory()
            ->for(Auth::user(), 'seller')
            ->create([
                'title' => $request->input('title'),
                'address_details' => $request->input('name'),
                'lat' => $request->float('lat'),
                'long' => $request->float('long'),
            ]);

        return $this->successResponse([$product]);
    }

    public function fakeService(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:100'],
            'lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
        ]);

        $service = Service::factory()
            ->for(Auth::user(), 'user')
            ->create([
                'title' => $request->input('title'),
                'location' => $request->input('name'),
                'lat' => $request->float('lat'),
                'long' => $request->float('long'),
            ]);

        return $this->successResponse([$service]);
    }

    public function fakeJob(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:100'],
            'lat' => ['required', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'long' => ['required', 'regex:/^[-]?((((1[0-7]\d)|(\d?\d))\.(\d+))|180(\.0+)?)$/'],
        ]);

        $job = JobAd::factory()
            ->for(Auth::user(), 'user')
            ->create([
                'title' => $request->input('title'),
                'location' => $request->input('name'),
                'lat' => $request->float('lat'),
                'long' => $request->float('long'),
            ]);

        return $this->successResponse([$job]);
    }
}
