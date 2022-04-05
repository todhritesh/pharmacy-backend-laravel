<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ManageStaff extends Controller
{
    public function makeResponse($data_key , $data , $msg , $status){
        return response()->json([
            "message" => $msg ,
            $data_key => $data ,
        ],$status);
    }

    public function getStaff($id = null){
        if($id!=null){
            if($staff = User::where([['id',$id],['role','staff']])->first()){
                return $this->makeResponse('staff',$staff,"data fetched",200);
            }else{
                return $this->makeResponse('staff',$staff,"not found",404);
            }
        }
        $data = User::where("role","staff")->get();
        if(!$data->count()){
            return $this->makeResponse('staff',$data,"not found",404);
        }
        return $this->makeResponse("staffs",$data , "data fetched" , 200);
    }

    public function addStaff(Request $req){

        $validator = Validator::make($req->all(),[
            "name"=>'required|string',
            "email"=>"required|email|unique:users,email",
            "phone"=>"required|numeric|digits:10|unique:users,phone",
            "password"=>"required|string|min:6|required_with:confirm_password|same:confirm_password",
            "confirm_password"=>"required|min:6",
        ]);

        if($validator->fails()){
            return $this->makeResponse('error',$validator->errors(),"validation error",403);
        }

        $user = new User();
        $user->name = $req->name;
        $user->email = $req->email;
        $user->phone = $req->phone;
        $user->password = Hash::make($req->password);
        $user->isverified = 1;
        $saved = $user->save();

        if($saved){
            return $this->makeResponse('user',$user,'saved successfully',201);
        }
        return $this->makeResponse('user',null,'something went wrong',500);

    }


    public function editStaff(Request $req,$id=null){
        if($id==null)
            return $this->makeResponse('id',"cannot be null","not found",404);

        $staff = User::where([['id',$id],['role','staff']])->first();
        if(!$staff){
            return $this->makeResponse('staff',null,"not found",404);
        }

        $validator = Validator::make($req->all(),[
            "name"=>'required|string',
            "email"=>"required|email|unique:users,email,$id,id",
            "phone"=>"required|numeric|digits:10|unique:users,phone,$id,id",
        ]);

        if($validator->fails()){
            return $this->makeResponse('error',$validator->errors(),"validation error",403);
        }

        $staff->name = $req->name;
        $staff->email = $req->email;
        $staff->phone = $req->phone;
        $updated = $staff->update();

        if($updated){
            return $this->makeResponse('staff',$staff,'updated successfully',201);
        }
        return $this->makeResponse('staff',null,'something went wrong',500);

    }

    public function verifyStaff($id=null){
        if($id==null)
            return $this->makeResponse('id',"cannot be null","not found",404);

        $staff = User::where([['id',$id],['role','staff']])->first();
        if(!$staff){
            return $this->makeResponse('verified',null,"not found",404);
        }elseif($staff->isverified){
            return $this->makeResponse('verified',1,"already verified",200);
        }

        $staff->isverified = 1;
        $updated = $staff->update();
        if($updated){
            return $this->makeResponse('verified',1,"verified successfully",200);
        }
    }
}
