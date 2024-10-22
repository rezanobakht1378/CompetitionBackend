<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="fullname",
     *                     type="string",
     *                     description="Full name of the user"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="Phone number of the user (9 to 13 digits)"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Password for the user account"
     *                 ),
     *                 @OA\Property(
     *                     property="c_password",
     *                     type="string",
     *                     description="Confirmation password"
     *                 ),
     *                 required={"fullname", "email", "phone", "password", "c_password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\Schema(ref="#/definitions/RegisterResponse")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or phone already registered"
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            // 'email' => 'required|email',
            'phone' => 'required|min:9|max:13',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input["name"] = Str::lower($input["fullname"]);
        // $em = explode('@', $input["email"]);
        // $em[0] = str_replace('.', '', $em[0]);
        // $input["email"] = $em[0] . '@' . $em[1];
        // $user_existance = User::where('email', $input["email"])->orWhere('phone', $input["phone"])->count();
        $user_existance = User::Where('phone', $input["phone"])->count();
        if ($user_existance > 0) {
            return $this->sendError('Register Failed Error.', "Phone number Already registered");
        }

        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken($user->fullname)->plainTextToken;
        $success['name'] = $user->fullname;

        return $this->sendResponse($success, 'User registered successfully.');
    }

    /**
     * Login api
     *
     * @OA\Post(
     *     path="/login",
     *     summary="User login",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="phone of the user"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Password for the user account"
     *                 ),
     *                 required={"email", "password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\Schema(ref="#/definitions/LoginResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorised: Invalid email or password"
     *     )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['phone' => $request->phone, 'password' => $request->password])) {
            $user = Auth::user();
            $user->tokens()->delete();
            $success['token'] = $user->createToken($user->fullname)->plainTextToken;
            $success['name'] = $user->fullname;

            return $this->sendResponse($success, 'User logged in successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    /**
     * Logout api
     *
     * @OA\Post(
     *     path="/logout",
     *     summary="User logout",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorised: User not logged in"
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse(true, 'Logged out successfully');
    }
}
