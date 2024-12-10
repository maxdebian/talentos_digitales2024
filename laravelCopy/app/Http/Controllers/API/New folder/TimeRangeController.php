<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\nucooks\entities\TimeRanges;
use Carbon\Carbon;
/* use Carbon\Carbon;
use DateTime; */
use Notification;

class TimeRangeController extends Controller
{
    public function list(){
        $timeRange = TimeRanges::first();

        $interval = strtotime('01:00:00');
        $intervalFormat = date('H:i:s',$interval);
        $horaInitial = strtotime('06:00:00');
        $horaInitialFormat = date('H:i:s',$horaInitial);
        $selectTime=[];
        $selectInterval=[];
        for($x=0;$x<4;$x++){
            array_push($selectInterval, $intervalFormat);
            $intervalFormat = strtotime('+1 hour',strtotime( $intervalFormat));
            $intervalFormat = date('H:i:s',$intervalFormat);
        }
        for($x=0;$x<14;$x++){
            $horaInitialFormat = strtotime('+1 hour',strtotime( $horaInitialFormat));
            $horaInitialFormat = date('H:i:s',$horaInitialFormat);
            array_push($selectTime, $horaInitialFormat);
        }

        return view('admin.timerange.list',compact('timeRange','selectTime','selectInterval')) ;
    }

    public function update(Request $request){
        $timeRange = TimeRanges::where('id',$request->id)->first();
        $timeRange->hourInitial = $request->hourInitial;
        $timeRange->hourFinaly = $request->hourFinaly;
        $timeRange->interval = $request->interval;
        $timeRange->save();
        if(!is_null($timeRange)){
            $timeRangeStatus = 1;
        }else{
            $timeRangeStatus = 0;
        }


        return redirect('timerange/list')->with('timeRangeStatus',$timeRangeStatus);

    }

    public function timerange(){
        $timeRange = TimeRanges::first();

        $horaInitial = strtotime($timeRange->hourInitial);
        $horaInitialFormat = date('H:i:s',$horaInitial);
        $selectTime=[];
        $selectTimeEnd=[];
        $partHI = explode(':',$timeRange->hourInitial);
        $partHF = explode(':',$timeRange->hourFinaly);
        $diff = $partHF[0] - $partHI[0];

        for($x=0;$x<=$diff; $x++){
            array_push($selectTime,$horaInitialFormat);
            $horaInitialFormat = strtotime('+1 hour',strtotime( $horaInitialFormat));
            $horaInitialFormat = date('H:i:s',$horaInitialFormat);
        }
        $interval = Carbon::parse($timeRange->interval)->format('h');
        foreach ($selectTime as $key => $selectTimeOrigin) {
            $horaInitialFormat = strtotime('+'.$interval.' hour',strtotime( $selectTimeOrigin));
            $horaInitialFormat = date('H:i:s',$horaInitialFormat);
            $selectTimeEnd[$key] = $horaInitialFormat;
        }

        if(!is_null($timeRange)){
            return response()->json([
                'status'            => '200',
                'timeRange'         => $selectTime,
                'timeRangeInterval' => $selectTimeEnd,
                'interval'          =>  $timeRange->interval
                ]);
        }
        return response()->json([
           'status' => '400',
           'timeRange' => 0,
           'interval'  =>  0
        ]);

    }
}
