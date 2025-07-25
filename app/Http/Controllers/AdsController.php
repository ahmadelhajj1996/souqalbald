<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ads\JobRequest;
use App\Http\Requests\Ads\ProductRequest;
use App\Http\Requests\Ads\ServiceRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdsController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_type' => 'required|in:product,service,job',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'invalid_ad_type',
                'ads',
                422
            );
        }

        switch ($validator->safe()->input('ad_type')) {
            case 'product':
                $prodReq = ProductRequest::createFrom($request);
                $prodReq->setContainer(app())->setRedirector(app('redirect'));

                $validator = app('validator')->make(
                    $prodReq->all(),
                    $prodReq->rules(),
                    $prodReq->messages()
                );

                if ($validator->fails()) {
                    throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => __('validation.failed'),
                        'errors' => $validator->errors(),
                    ], 422));
                }

                return app(ProductController::class)->store($prodReq);

            case 'service':

                $svcReq = ServiceRequest::createFrom($request);
                $svcReq->setContainer(app())->setRedirector(app('redirect'));

                $validator = app('validator')->make(
                    $svcReq->all(),
                    $svcReq->rules(),
                    $svcReq->messages()
                );

                if ($validator->fails()) {
                    throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => __('validation.failed'),
                        'errors' => $validator->errors(),
                    ], 422));
                }

                return app(ServiceController::class)->store($svcReq);
            case 'job':
                $jobReq = JobRequest::createFrom($request);
                $jobReq->setContainer(app())->setRedirector(app('redirect'));

                $validator = app('validator')->make(
                    $jobReq->all(),
                    $jobReq->rules(),
                    $jobReq->messages()
                );

                if ($validator->fails()) {
                    throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => __('validation.failed'),
                        'errors' => $validator->errors(),
                    ], 422));
                }

                return app(JobController::class)->store($jobReq);
            default:
                return $this->errorResponse(
                    'Invalid_ad_type',
                    'messages',
                    422
                );
        }
    }
}
