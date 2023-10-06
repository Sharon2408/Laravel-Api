<?php
namespace App\Http\Controllers;

use App\Events\LinemenCredentialsMailEvent;
use App\Mail\LinemenCredentialsMail;
use App\Models\Complaint;
use App\Models\Lineman;
use App\Models\Task;
use DB;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Events\ConsumerIdMailEvent;

class LinemanController extends Controller
{
    // Lineman Registeration
    public function linemanRegister(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:linemen',
                'password' => 'required|string|min:6',
                 'email.unique' => 'The email address has already been taken.',
                'phone_no' => 'required',
                'lineman_id' => 'required',
            ]);
            $user = Lineman::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone_no' => $request->input('phone_no'),
                'lineman_id' => $request->input('lineman_id'),
                'password' => Hash::make($request->input('password')),
            ]);
            $data = [
                'name'=>$request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password')
            ];
             event(new LinemenCredentialsMailEvent($data));
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (QueryException $q) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
        ]);
    }

    // Handles Lineman Login
    public function linemanLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            $credentials = $request->only('email', 'password');

            $token = auth()->guard('linemen')->attempt($credentials);
            Auth::shouldUse('linemen');

            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Credentials',
                ], 401);
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
        } catch (ValidationException $q) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        }
    }

    // This Method Retrives the area wise Available Lineman from the Lineman Table
    public function viewLineman($id)
    {
        try {
            $viewLineman = Lineman::where('lineman_id', $id)->get();
        } catch (QueryException $q) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        }
        return $viewLineman;
    }

    // After retreiving the lineman Area Wise The tasks are assigned to the available linemen
    public function assignTasktoLineman(Request $request)
    {
        try {
            Task::create([
                'complaint_id' => $request->input('complaint_id'),
                'lineman_id' => $request->input('lineman_id')
            ]);

        } catch (QueryException $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Database error'
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Task Assigned'
        ]);
    }

    //  This Method is used to show the lineman wise tasks for the current logged in Lineman
    public function viewLinemanTasks($lineman_id)
    {
        try {
            $viewLinemanTasks = Complaint::join('tasks', 'complaints.id', '=', 'tasks.complaint_id')
                ->where('tasks.lineman_id', '=', $lineman_id)->get();
        } catch (QueryException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        }
        return $viewLinemanTasks;
    }

    // To show the Status in the of the Task 
    public function getStatus()
    {
        try {
            $status = DB::table('status')->select()->get();
            return $status;
        } catch (QueryException $q) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $complaint = Complaint::find($request->input('complaint_id'));
            $complaint->status = $request->input('status_id');
            $complaint->save();
            
            $task = Task::find($request->input('task_id'));
            $task->status = $request->input('status_id');
            $task->save();
            return response()->json([
                'status' => 'succes',
                'message' => 'Updated'
            ]);
        } catch (QueryException $q) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        }
    }
    public function updateSolvedDate(Request $request){
        try{
            $complaint = Complaint::find($request->input('complaint_id'));
            $complaint->solved_date = Carbon::now();
            $complaint->save();
            return response()->json([
                'status' => 'succes',
                'message' => 'Updated'
            ]);
        }catch (QueryException $q) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error'
            ], 500);
        }
    }
}