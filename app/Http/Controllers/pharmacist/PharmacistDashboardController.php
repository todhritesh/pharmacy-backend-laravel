<?php

namespace App\Http\Controllers\pharmacist;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class PharmacistDashboardController extends Controller
{
    function pharmacistDashboard(){
        $total_stock = Stock::count();
        $expired_stock = Stock::whereDate('exp','<=',date('Y-m-d'))->count();
        $sellable_stock = Stock::whereDate('exp','>',date('Y-m-d'))->count();
        return response()->json([
            'message'=>'data fetched',
            'dashboard'=>[
                'Total stocks' => ["count"=>$total_stock,"to"=>"/pharmacist/manage-stocks/"],
                'Expired Stocks' => ["count"=>$expired_stock,"to"=>"/pharmacist/manage-stocks/expired"],
                'Sellable Stocks'=>["count"=>$sellable_stock,"to"=>"/pharmacist/manage-stocks/sellable"]
            ]
        ],200);
    }
}
