<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ads\ServiceRequest;
use App\Models\Service;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    use ApiResponseTrait;

    public function store(ServiceRequest $request)
    {
        $user = auth()->user();
        $service = Service::create([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'governorate' => $request->governorate,
            'location' => $request->location,
            'days_hours' => $request->days_hours,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'added_by' => $user->id,
            'long' => $request->long,
            'lat' => $request->lat,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($index >= 5) {
                    break;
                }

                $service->images()->create([
                    'image' => $image->store('services', 'public'),
                ]);
            }
        }

        return $this->successResponse($service->load(['costs','images']), 'service', 'service_created_successfully');
    }

    public function index()
    {
        $query = Service::with(['costs','images']);
        $services = $query->get();

        return $this->successResponse($services, 'service', 'servicesـretrievedـsuccessfully');
    }

    public function getUserServices()
    {
        $user = auth()->user();
        $query = Service::where('added_by', $user->id)->with(['costs','images']);
        $services = $query->get();

        return $this->successResponse($services, 'service', 'servicesـretrievedـsuccessfully');
    }

    public function toggleActivation(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
        ]);
        $service = Service::find($request->input('service_id'));
        if ($service == null) {
            return $this->errorResponse('Not found');
        }
        $service->update(['is_active' => ! $service->is_active]);

        return $this->successResponse(message: 'updated');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
        ]);
        $service = Service::find($request->input('service_id'));
        if (
            $service === null ||
            (
                $service->added_by !== $request->user()->id &&
                ! Auth::user()->hasRole('admin')
            )
        ) {
            return $this->errorResponse('Not found');
        }
        $service->delete();

        return $this->successResponse(message: 'deleted');
    }
}
