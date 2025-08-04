<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ads\JobRequest;
use App\Models\JobAd;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    use ApiResponseTrait;

    public function store(JobRequest $request)
    {
        $user = auth()->user();

        $job = JobAd::create([
            'title' => $request->title,
            'job_type' => $request->job_type,
            'governorate' => $request->governorate,
            'location' => $request->location,
            'salary' => $request->salary,
            'education' => $request->education,
            'experience' => $request->experience,
            'skills' => $request->skills,
            'description' => $request->description,
            'work_hours' => $request->work_hours,
            'start_date' => $request->start_date,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'added_by' => $user->id,
            'job_title' => $request->job_title,
            'type' => $request->type,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if ($index >= 5) {
                    break;
                }

                $job->images()->create([
                    'image' => $image->store('jobs', 'public'),
                ]);
            }
        }

        return $this->successResponse($job->load(['costs','images']), 'job', 'job_created_successfully');
    }

    public function index()
    {
        $query = JobAd::with(['costs','images']);
        $jobs = $query->get();

        return $this->successResponse($jobs, 'job', 'jobsـretrievedـsuccessfully');
    }

    public function getUserJobs()
    {
        $user = auth()->user();
        $query = JobAd::where('added_by', $user->id)->with(['costs','images']);
        $jobs = $query->get();

        return $this->successResponse($jobs, 'job', 'jobsـretrievedـsuccessfully');
    }

    public function toggleActivation(Request $request)
    {
        $request->validate([
            'job_id' => ['required', 'exists:job_ads,id'],
        ]);
        $job = JobAd::find($request->input('job_id'));
        if ($job == null) {
            return $this->errorResponse('Not found');
        }
        $job->update(['is_active' => ! $job->is_active]);

        return $this->successResponse(message: 'updated');
    }

    public function delete(Request $request)
    {
        $request->validate([
            'job_id' => ['required', 'exists:job_ads,id'],
        ]);
        $job = JobAd::find($request->input('job_id'));
        if (
            $job === null ||
            (
                $job->added_by !== $request->user()->id &&
                ! Auth::user()->hasRole('admin')
            )
        ) {
            return $this->errorResponse('Not found');
        }
        $job->delete();

        return $this->successResponse(message: 'deleted');
    }
}
