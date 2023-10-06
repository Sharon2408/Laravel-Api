<?php

namespace App\Http\Controllers;

use App\Events\ConsumerIdMailEvent;
use App\Models\Complaint;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['userLogin', 'userRegister']]);
    }

    // Validates the user Login Credentials
    public function userLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            $credentials = $request->only('email', 'password');
            $token = Auth::attempt($credentials);

            if (!$token) {
                throw new Exception('Invalid Credentials');
            } else {
                $user = Auth::user();
                return response()->json([
                    'status' => 'success',
                    'user' => $user,
                    'authorisation' => [
                            'token' => $token,
                            'type' => 'bearer',
                            'name' => $user->name,
                            'email' => $user->email
                        ]
                ]);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        }
    }

    // Function Called when User Registration takes Place
    public function userRegister(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'email.unique' => 'The email address has already been taken.',
                'phoneno' => 'required',
                'consumer_id' => 'required',
            ]);
            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phoneno' => $request->input('phoneno'),
                'consumer_id' => $request->input('consumer_id'),
                'password' => Hash::make($request->input('password')),
            ]);
            $data = [
                'email' => $request->email,
                'consumer_id' => $request->consumer_id
            ];
            // event(new ConsumerIdMailEvent($data));
        } catch (QueryException $q) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        } catch (ValidationException $q) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation Error'
            ], 422);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
        ]);
    }
}