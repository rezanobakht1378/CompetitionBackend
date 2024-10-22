<?php

namespace App\Http\Controllers\API;

/**
 * @OA\Schema(
 *     schema="Competition",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Competition Name"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 * )
 */

use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;

class CompetitionController extends BaseController
{
    /**
     * Create Competition api
     *
     * @OA\Post(
     *     path="/competition/create",
     *     tags={"Competitions"},
     *     summary="Create a new competition",
     *     description="This endpoint creates a new competition.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Competition Name"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Competition created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/Competition"),
     *             @OA\Property(property="message", type="string", example="Competition created successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation Error."),
     *             @OA\Property(property="validation_errors", type="object"),
     *         ),
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['name' => 'required']);
        if ($validator->fails())
            return $this->sendError('Validation Error.', $validator->errors());
        $comptetition = Competition::create($request->all());
        return $this->sendResponse($comptetition, "Competition created successfully", 201);
    }
}
