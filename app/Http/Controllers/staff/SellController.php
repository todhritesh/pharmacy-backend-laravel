<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sell;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Boolean;

class SellController extends Controller
{
    public function makeResponse($data_key , $data , $msg , $status){
        return response()->json([
            "message" => $msg ,
            $data_key => $data ,
        ],$status);
    }

    function availableStocks(){
        $stocks = Stock::with('addedBy')->whereDate('exp','>',date('Y-m-d'))->get();
        return $this->makeResponse('stocks',$stocks,'data fetched',200);
    }

    function autocompleteData(){
        $stocks = Stock::with('addedBy')
        ->select('id','name','brand','gst','remaining_qty','exp','mfd','sp','image')
        ->whereDate('exp','>',date('Y-m-d'))->get();
        return $this->makeResponse('stocks',$stocks,'data fetched',200);
    }

    function staffDashboard(){
        $available_stocks = Stock::whereDate('exp','>',date('Y-m-d'))->count();

        $data = [
            'Available stocks'=>["count"=>$available_stocks,"to"=>'/staff/available/stocks'],
            'Sell'=>["count"=>' ',"to"=>'/staff/sell/'],
        ];

        return $this->makeResponse('dashboard',$data,'data fetched',200);
    }


    function sellStock(Request $req ){
        if($req->has('customer_details.email')){
            $validator = Validator::make($req->all(),[
                'items.*.id'=>'required|numeric|exists:stocks,id',
                'items.*.qty'=>'required|min:1|numeric',
                'customer_details.email'=>'required|string|email',
                'customer_details.name'=>'required|string',
                'customer_details.phone'=>'required|numeric|digits:10',
            ]);

            if($validator->fails()){
                return $this->makeResponse('error',$validator->errors(),'validation_error',403);
            }
        }

        if($req->has('customer_details.old_phone')){

            $validator = Validator::make($req->all(),[
                'items.*.id'=>'required|numeric|exists:stocks,id',
                'items.*.qty'=>'required|min:1|numeric',
                'customer_details.old_phone'=>'required|numeric|digits:10',
            ]);

            if($validator->fails()){
                return $this->makeResponse('error',$validator->errors(),'validation_error',403);
            }
        }


        $customer_id ='';
        if($req->has('customer_details.email')){
            $customer = new Customer();
            $c = $req->customer_details;
            $customer->name = $c['name'];
            $customer->email = $c['email'];
            $customer->phone = $c['phone'];
            $saved = $customer->save();
            $customer_id = $customer->id;
        }

        if($req->has('customer_details.old_phone')){

            $customer = Customer::where('phone',$req->customer_details['old_phone'])->first();
            $customer_id = $customer->id;
        }


        $purchased = [];
        $items = $req->items;

        $lastId = Sell::orderBy('id','desc')->first();
        if($lastId==null){
            $lastId = 0;
        }else{
            $lastId = $lastId->id;
        }

        $sell_id = $lastId;
        $staff = auth('admin_api')->user()->name;
        foreach($items as $i){
            $stock = Stock::with('addedBy')->find($i['id']);
            $pharmacist = $stock->addedBy['name'];
            $qty = $i['qty'];
            $stock_qty = $stock->remaining_qty;
            $sell = new Sell();
            $sell->medicine = $stock->name;
            $sell->customer_id = $customer_id;
            $sell->sell_id = $sell_id;
            $sell->brand = $stock->brand;
            $sell->category = $stock->category;
            $sell->price_per_peice = $stock->sp;
            $sell->qty = $qty;
            $sell->gst = $stock->gst;
            $gst = ($stock_qty * $stock->sp * $stock->gst)/100;
            $total_price = $stock_qty * $stock->sp + $gst;
            $sell->total_price = $total_price;
            $sell->seller = $staff;
            $sell->pharmacist = $pharmacist;
            $sell->image_link = $stock->image_link;
            $saved = $sell->save();
            array_push($purchased,$sell);
            $stock->remaining_qty = $stock->remaining_qty - $qty;
            $stock->update();
        }

        return $this->makeResponse('purchased',$purchased,'successfully placed order','200');


    }

    function checkUser(Request $req,$status=null){
        if($status=='new'){
            $validator = Validator::make($req->all(),[
                'email'=>'required|email|unique:customers,email',
                'name'=>'required|string',
                'phone'=>'required|numeric|digits:10|unique:customers,phone',
            ]);
        }elseif($status=='old'){
            $validator = Validator::make($req->all(),[
                'old_phone'=>'required|numeric|digits:10|exists:customers,phone',
            ]);
        }else{
            return $this->makeResponse('error',null,'Invalid attempt',403);
        }

        if($validator->fails()){
            return $this->makeResponse('error',$validator->errors(),'validation_error',403);
        }

        return $this->makeResponse('verified',true,'customer verified',200);
    }
}
