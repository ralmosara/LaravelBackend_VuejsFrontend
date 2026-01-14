<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $schedules = Schedule::where('user_id', auth()->id())
            ->orderBy('start_time')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => ScheduleResource::collection($schedules)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        
        $schedule = Schedule::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Event scheduled successfully',
            'data' => new ScheduleResource($schedule)
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule): JsonResponse
    {
        $this->authorize('view', $schedule);
        
        return response()->json([
            'success' => true,
            'data' => new ScheduleResource($schedule)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule): JsonResponse
    {
        $this->authorize('update', $schedule);
        
        $schedule->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => new ScheduleResource($schedule)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule): JsonResponse
    {
        $this->authorize('delete', $schedule);
        
        $schedule->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }
}
