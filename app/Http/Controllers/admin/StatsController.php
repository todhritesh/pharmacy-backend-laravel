<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Sell;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class StatsController extends Controller
{
    public function makeResponse($data_key , $data , $msg , $status){
        return response()->json([
            "message" => $msg ,
            $data_key => $data ,
        ],$status);
    }

    function monthlySell(){
         $data = Sell::selectRaw('year(created_at) as year, monthname(created_at) as month, sum(total_price) as total_sale')
        ->groupBy('month','year')
        ->orderByRaw('min(created_at) asc')
        ->get();
        // $month = ['January','February','March','April','May','July ','August','September','October','November','December',];
        // $new_data = [];
        // $min_max_year = Sell::selectRaw('year(min(created_at)) as min_year , year(max(created_at)) as max_year')->get();

        return $this->makeResponse('stats',$data,'data fetched',200);
    }
}
