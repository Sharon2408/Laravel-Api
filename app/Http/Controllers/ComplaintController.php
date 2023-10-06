<?php

namespace App\Http\Controllers;

use App\Events\ConsumerIdMailEvent;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ComplaintController extends Controller
{
  public function getDistrict()
  {
    try {
      $district = DB::table('districts')->get();
      return response()->json($district);
    } catch (QueryException $q) {
      return response()->json([
        'status' => 'error',
        'message' => 'Database Error'
      ], 500);
    }
  }
  public function getZone()
  {
    try {
      $zone = DB::table('zones')->get();
      return response()->json($zone);
    } catch (QueryException $q) {
      return response()->json([
        'status' => 'error',
        'message' => 'Database Error'
      ], 500);
    }
  }

  public function getArea()
  {
    try {
      $area = DB::table('areas')->get();
      return response()->json($area);
    } catch (QueryException $q) {
      return response()->json([
        'status' => 'error',
        'message' => 'Database Error'
      ], 500);
    }
  }
  public function storeComplaints(Request $request)
  {
    try {
      $request->validate([
        'user_id' => 'required',
        'consumer_id' => 'required|string',
        'issue_details' => 'required|string',
      ]);
      $user = User::where('consumer_id', $request->input('consumer_id'))->first();

      if (!$user) {
        return response()->json([
          'status' => 'error',
          'message' => 'Consumer ID not found.',
        ], 404);
      } else {
        $complaint = Complaint::create([
          'user_id' => $request->input('user_id'),
          'consumer_id' => $request->input('consumer_id'),
          'issue_details' => $request->input('issue_details'),
          'landmark' => $request->input('landmark')
        ]);
      }
    } catch (ValidationException $q) {
      return response()->json([
        'status' => 'error',
        'message' => 'Validation Error'
      ], 422);
    } catch (QueryException $e) {
      return response()->json([
        'status' => 'error',
        'message' => 'Database Error'
      ], 500);
    }
    return response()->json([
      'status' => 'success',
      'message' => 'Complaint created successfully',
      'complaint' => $complaint,
    ]);

  }

  public function getComplaints()
  {
    try {
      $complaints = Complaint::all();
      return $complaints;
    } catch (QueryException $q) {
      return response()->json([
        'status' => 'error',
        'message' => 'Database Error'
      ], 500);
    }
  }


  public function userRegisteredComplaints($user_id){

    try{
     $complaints = Complaint::where('user_id',$user_id)->with('complaint')->get();
      return $complaints;
    }catch(QueryException $q){
      return response()->json([
        'status' => 'error',
        'message' => 'Database Error'
      ], 500);
    }
  }
}