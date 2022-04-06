<?php

namespace App\Http\Controllers\pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Stock;
// use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ManageStocks extends Controller
{
    public function makeResponse($data_key , $data , $msg , $status){
        return response()->json([
            "message" => $msg ,
            $data_key => $data ,
        ],$status);
    }

    function getStocks($id = null){

        if($id!=null){
            if($stock = Stock::with('addedBy')->find($id)){
                return $this->makeResponse('stock',$stock,"data fetched",200);
            }else{
                return $this->makeResponse('staff',$stock,"not found",404);
            }
        }
        $data = Stock::with('addedBy')->get();
        // return $data[2]->image_link;
        return $this->makeResponse("stocks",$data , "data fetched",200);
    }

    function get_expired_or_sellable_stocks($status=null){
        if($status=='expired'){
            $stock = Stock::with('addedBy')->whereDate('exp','<=',date('Y-m-d'))->get();
        }elseif($status == 'sellable'){
            $stock = Stock::with('addedBy')->whereDate('exp','>',date('Y-m-d'))->get();
        }else{
            $stock = Stock::with('addedBy')->get();
        }
        return $this->makeResponse('stocks',$stock,"data fetched",200);
    }

    function addStock(Request $req){
        $validator = Validator::make($req->all(),[
            'name'=>'required',
            'mfd'=>'required|date|date_format:Y-m-d|before:today',
            'exp'=>"required|date|date_format:Y-m-d|after:mfd",
            'total_qty'=>"required|numeric|min:50",
            'price_per_peice'=>"required|numeric",
            'brand'=>"required|string",
            'category'=>"required|string",
            'image'=>"required|mimes:jpeg,jpg,png",
            'gst'=>"required|numeric",
            'sp'=>"required|numeric",
        ]);

        if($validator->fails()){
            return $this->makeResponse("validation_error",$validator->errors(),"validation failed",403);
        }

        $img_ext = $req->image->getClientOriginalExtension();
        $img_name = date('YmdHis').'.'.$img_ext;

        $stock = new Stock();
        $stock->name = $req->name;
        $stock->total_qty = $req->total_qty;
        $stock->remaining_qty = $req->total_qty;
        $stock->brand = $req->brand;
        $stock->category = $req->category;
        $stock->exp = $req->exp;
        $stock->mfd = $req->mfd;
        $stock->gst = $req->gst;
        $stock->sp = $req->sp;
        $total_cost = $req->qty * $req->price_per_peice + ($req->qty * $req->price_per_peice * $req->gst)/100;
        $stock->total_cost = $total_cost;
        $stock->price_per_peice = $req->price_per_peice;
        $stock->user_id = auth()->user()->id;
        $stock->image = $img_name;
        $req->image->storeAs("public/stocks" , $img_name);
        $saved = $stock->save();
        $stock = Stock::with('addedBy')->find($stock->id);

        // $stock->image = asset("storage/stocks/".$stock->image);
        if($saved){
            return $this->makeResponse("stock",$stock,"successfully saved",201);
        }

        return $this->makeResponse("stock",null,"unable to save data",500);

    }


    function editStock(Request $req , $id = null){
        if($id==null){
            return $this->makeResponse('stock',null,"invalid stock id",404);
        }

        if(!$stock = Stock::find($id)){
            return $this->makeResponse('stock',null,"invalid stock id",404);
        }

        $validator = Validator::make($req->all(),[
            'name'=>'required',
            'mfd'=>'required|date|date_format:Y-m-d|before:today',
            'exp'=>"required|date|date_format:Y-m-d|after:mfd",
            'total_qty'=>"required|numeric|min:50",
            'remaining_qty'=>"required|numeric|lt:total_qty",
            'price_per_peice'=>"required|numeric",
            'brand'=>"required|string",
            'gst'=>"required|numeric",
            'sp'=>"required|numeric",
        ]);

        if($validator->fails()){
            return $this->makeResponse("validation_error",$validator->errors(),"validation failed",403);
        }

        $stock->name = $req->name;
        $stock->total_qty = $req->total_qty;
        $stock->remaining_qty = $req->remaining_qty;
        $stock->brand = $req->brand;
        $stock->category = $req->category;
        $stock->exp = $req->exp;
        $stock->mfd = $req->mfd;
        $stock->gst = $req->gst;
        $stock->sp = $req->sp;
        $total_cost = $req->qty * $req->price_per_peice + ($req->qty * $req->price_per_peice * $req->gst)/100;
        $stock->total_cost = $total_cost;
        $stock->price_per_peice = $req->price_per_peice;
        $stock->user_id = auth()->user()->id;
        $updated = $stock->update();
        $stock = Stock::with('addedBy')->find($stock->id);
        // $stock->image = asset("storage/stocks/".$stock->image);
        if($updated){
            return $this->makeResponse("stock",$stock,"successfully updated",200);
        }

        return $this->makeResponse("stock",null,"unable to update data",500);

    }

    function deleteStock($id = null){

        if($id==null){
            return $this->makeResponse('stock',null,"invalid stock id",404);
        }

        if(!$stock = Stock::find($id)){
            return $this->makeResponse('stock',null,"invalid stock id",404);
        }
        // Storage::delete(public_path("public/storage/stocks/$stock->image"));

        $deleted = $stock->delete();

        if($deleted){
            return $this->makeResponse("stock_id",$id,"successfully deleted",200);
        }

        return $this->makeResponse("stock_id",$id,"unable to delete data",500);

    }
}
