<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function makeResponse($data_key , $data , $msg , $status){
        return response()->json([
            "message" => $msg ,
            $data_key => $data ,
        ],$status);
    }
    function adminDashboard(){
        $staff = User::where([['role','staff'],['isverified',1]])->count();
        $pharmacist = User::where([['role','pharmacist'],['isverified',1]])->count();
        $stocks = User::count();

        $data = [
            'Staff'=>["count"=>$staff,"to"=>'/admin/manage-staff'],
            'Pharmacist'=>["count"=>$pharmacist,"to"=>'/admin/manage-pharmacist'],
            'Stock Batch'=>["count"=>$stocks,"to"=>'/view-stock']
        ];

        return $this->makeResponse('dashboard',$data,'data fetched',200);
    }
}
