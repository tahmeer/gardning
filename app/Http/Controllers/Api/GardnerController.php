<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class GardnerController extends Controller
{
    // public function __construct() {
    //     $this->middleware('auth:gardner');
    // }
    public function MyBooking(){
        try{
            $myBookings = Booking::where('awarded_to',\Auth::id())->get();
            if(count($myBookings) > 0){
                return response(['message' => 'Success', 'data' => $myBookings], 200);

            }else{
                return response(['message' => 'failure', 'error' => 'You Have Not Booking Yet'], 422);

            }

        }catch (\Throwable $th) {
            return response(['message' => 'failure', 'error' => $th->getMessage()], 500);
        }

    }
    public function logout(){
        $user = Auth::guard('gardner')->user()->token();
        $user->revoke();
        return response()->json(['message'=>'Success','data'=>'Logout Successfully'],200);
    }
    public function index(){
        return "yes your are gardner";
    }
}
