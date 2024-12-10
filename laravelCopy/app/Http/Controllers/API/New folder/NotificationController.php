<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\nucooks\entities\MercadopagoPayments;
use App\User;


class NotificationController extends Controller
{
    public function notification(){
        $totalNotification = MercadopagoPayments::with(['user' => function($queryData) {
            $queryData->with('userdata')->get();
        }])->get();

        $countNotification = MercadopagoPayments::with(['user' => function($queryData) {
            $queryData->with('userdata')->get();
        }])->orderBy('created_at', 'DESC')->take(7)->get();
        return response()->json([
            'statusStock' => '200',
            'countNotification' => $totalNotification->count(),
            'userData'  => $countNotification,
            ]);
    }
    public function notificationUser(MercadopagoPayments $idMercadoCart){

        $totalNotification = MercadopagoPayments::with(['user' => function($queryData) {
            $queryData->with('userdata')->get();
        }])->orderBy('created_at', 'DESC')->get();

        /* $countNotification = MercadopagoPayments::with('user')->with('userdata')->where('id',$idMercadoCart->id)->first(); */
        $countNotification = MercadopagoPayments::where('id',$idMercadoCart->id)->with(['user' => function($queryData) {
            $queryData->with('userdata')->get();
        }])->first();;
        return response()->json([
            'statusStock' => '200',
            'countNotification' => $totalNotification->count(),
            'userData'  => $countNotification,
            ]);
    }
}
