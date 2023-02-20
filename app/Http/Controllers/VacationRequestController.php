<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVacationRequestRequest;
use App\Http\Requests\AcceptVacationRequestRequest;
use App\Http\Requests\UpdateVacationRequestRequest;

use Illuminate\Support\Facades\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Models\VacationRequest;
use App\Models\User;

class VacationRequestController extends Controller
{
    /**
     * Creates a new VacationRequest record.
     * 
     * If user already has pending pending VacationRequest, he will not be able to create new one, while getting message what to do.
     */
    public function store(CreateVacationRequestRequest $request)
    {
        $props = $request->only(['start', 'end']);

        if(auth()->user()->pending_vacation_request){
            return response()->json([
                'message' => "you already have pending vacation request. Please wait until it is resolved before submitting another request."
            ]);
        }

        $vacationRequest = new VacationRequest();
        
        $vacationRequest->user_id  = auth()->user()->id;
        $vacationRequest->start  = $props['start'];
        $vacationRequest->end    = $props['end'];
        $vacationRequest->status = VacationRequest::STATUS_PENDING;
        $vacationRequest->working_days_duration = $request->calculated_working_days_duration;

        $vacationRequest->save();

        return response()->json($vacationRequest, 201);
    }

    /**
     * Approving VacationRequest sets its status to VacationRequest::STATUS_APPROVED 
     * and updates users currently available vacation days based on updated state.
     * 
     * User can now request new VacationRequest.
     */
    public function approveVacationRequest(AcceptVacationRequestRequest $request)
    {
        $vacationRequest = VacationRequest::find($request->id);
        $vacationRequest->status = VacationRequest::STATUS_APPROVED;
        $vacationRequest->save();

        $user = User::find($vacationRequest->user_id);
        $user->available_vacation_days -= $vacationRequest->working_days_duration;
        $user->update();
        
        return response()->json([
            'message' => 'Record updated successfully',
        ], 200);
    }

    /**
     * User can now request new VacationRequest.
     */
    public function rejectVacationRequest(AcceptVacationRequestRequest $request)
    {
        $vacationRequest = VacationRequest::find($request->id);
        $vacationRequest->status = VacationRequest::STATUS_REJECTED;
        $vacationRequest->save();

        return response()->json([
            'message' => 'Record updated successfully',
        ], 200);
    }

    /**
     * Fetches currently pending VacationRequest.
     * 
     * pending means status property of model is VacationRequest::STATUS_PENDING.
     */
    public function getMyPendingVacationRequest()
    {
        return response()->json(auth()->user()->pending_vacation_request);
    }

    /**
     * Deletes currently pending VacationRequest.
     */
    public function deleteMyPendingVacationRequest()
    {
        try {
            auth()->user()->pending_vacation_request()->delete();
            return response()->json([
                'message' => 'Record deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            throw new HttpResponseException(response()->json(['message' => 'Unable to delete model.'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }
    }

    /**
     * Get all pending VacationRequest from all users.
     */
    public function getAllPendingVacationRequests()
    {
        return response()->json(VacationRequest::where(['status' => VacationRequest::STATUS_PENDING])->get());
    }
    
    /**
     * Get all vacations requests auth user made.
     */
    public function getAllVacationRequests()
    {
        return response()->json(auth()->user()->all_vacation_requests);
    }

    /**
     * Update current VacationRequest.
     * 
     * @todo this one is most unstable. UpdateVacationRequestRequest is not reliable because code should support choise bewteen
     * submitting start date, end date or both. Code allows that, but validation rules cause problems.
     */
    public function update(UpdateVacationRequestRequest $request)
    {
        $props = $request->all();
        
        $vacationRequest = $props['vacationRequest'];

        $vacationRequest->start = $props['start'];
        $vacationRequest->end = $props['end'];
        $vacationRequest->working_days_duration = $props['calculated_working_days_duration'];

        $vacationRequest->update();
       
        return response()->json($vacationRequest, 200);
    }
}
