<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */

    public function createUser(Request $request)
    {

        // return response()->json([
        //     'status' => true,
        //     'data' => 'My data',
        //     "message" => 'my Message',
        // ], 200);

        try {
            //validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'avatar' => 'required',
                    'type' => 'required',
                    'open_id' => 'required',
                    'name' => 'required',
                    'email' => 'required',
                    //'password' => 'required|min:6',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $validated = $validateUser->validated();

            $map = [];
            $map['type'] = $validated['type'];
            $map['open_id'] = $validated['open_id'];

            $user = User::where($map)->first();

            // whether user has loged in or not
            if (empty($user->id)) {

                // user never been exist in our DB
                // assigning the user in the DB
                // this token is user id
                $validated['token'] = md5(uniqid() . rand(10000, 99999));
                $validated['created_at'] = Carbon::now();
                //$validated['password'] = Hash::make($validated['password']);
                $userID = User::insertGetId($validated);
                // return response()->json([
                //     'status' => true,
                //     'message' => 'User created successfully',
                //     'data' => $validated,
                // ], 200);
                $userInfo = User::where('id', '=', $userID)->first();
                $accessToken = $userInfo->createToken(uniqid())->plainTextToken;
                $userInfo->access_token = $accessToken;
                User::where('id', '=', $userID)->update(['access_token' => $accessToken]);

                return response()->json([
                    'code' => 200,
                    'msg' => 'User created successfully',
                    'data' => $userInfo,
                ], 200);
            }

            $accessToken = $user->createToken(uniqid())->plainTextToken;
            $user->access_token = $accessToken;
            User::where('open_id', '=', $validated['open_id'])->update(['access_token' => $accessToken]);
            return response()->json([
                'code' => 200,
                'msg' => 'User logged in successfully',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),

            ], 500);
        }
    }
    /**
     * Login The User
     * @param Request $request
     * @return User
     */

    public function loginUser(Request $request)
    {
        try {
            //validated
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt(($request->only((['email', 'password']))))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match woth our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();


            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfilly',
                'token' => $user->createToken("API TOKEN")->plainTextToken,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),

            ]);
        }
    }
}
