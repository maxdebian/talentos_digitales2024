<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\nucooks\entities\Language;
use Illuminate\Http\Request;
use App\nucooks\entities\Province;
use App\nucooks\entities\Location;
use Illuminate\Support\Facades\Auth;
use App\nucooks\entities\Testcsvbrasil;

class ProvinceController extends Controller
{
    public function province($codePostal)
    {

        $locationProvince = Location::where('cp', $codePostal)->with('province')->first();
        $locations = Location::select('id', 'location')->where('cp', $codePostal)->get();
        if (!is_null($locations)) {
            return response()->json([
                'status' => '200',
                'province' => $locationProvince->province->province,
                'locations' => $locations
            ]);
        } else {
            return response()->json([
                'status' => '400',
            ]);
        }
    }
    public function provincePais($codePostal, $pais)
    {
        if ($pais === 'ar')
            $pais = 'AR';
        if ($pais === 'pt')
            $pais = 'BR';
        if ($pais === 'usa')
            $pais = 'EN';


        if(Auth::user()){
            $lang = Language::where('id',Auth::user()->language_id)->first();

            if(!is_null($lang)){
                $pais = strtoupper($lang->lang);
                if($pais==='ES') $pais = 'AR';
                if($pais==='EN') $pais = 'EN';
                if($pais==='PT-BR') $pais = 'BR';

            }
        }
        $cant = strlen($codePostal);
        $provinceDescription = null;
        if ($cant < 4) {
            return response()->json([
                'status' => '400',
            ]);
        }
        if ($cant === 4) {

            $locations = Location::select('id', 'location', 'cp', 'province_id')->where('cp', 'like', $codePostal)->with(['province' => function ($queryProvince) use ($pais) {
                $queryProvince->where('country', $pais)->first();
            }])->groupby('location')->distinct()->get();
            $locations = $locations->filter(function ($location, $key) {
                return $location->province != null;
            });
            if (count($locations) > 0) {
                $province = Province::where('id', $locations[0]->province_id)->first();
                $provinceDescription = $province->province;
            }
        }
        if ($cant > 4) {
             $codP = substr($codePostal, 0, 5) . '___';
            $locations = Location::select('id', 'location', 'cp', 'province_id')->where('cp', 'like', $codP)->with(['province' => function ($queryProvince) use ($pais) {
                $queryProvince->where('country', $pais)->get();
            }])->groupby('location')->distinct()->get();
            $locations = $locations->filter(function ($location, $key) {
                return $location->province != null;
            });
            if (count($locations) > 0) {
                $province = Province::where('id', $locations[0]->province_id)->first();
                $provinceDescription = $province->province;
            }
        }






        if ((!is_null($locations)) and (!is_null($provinceDescription))) {
            return response()->json([
                'status' => '200',
                'province' => $provinceDescription,
                'locations' => $locations
            ]);
        } else {
            return response()->json([
                'status' => '400',
            ]);
        }
    }



    public function testProvinceBR()
    {
        /*        $provinces = Province::where('country', 'BR')->get();
        foreach ($provinces as $valProvince) { */
        $locationsBrasil = Testcsvbrasil::where('estado', 'DF')->get();
        foreach ($locationsBrasil as $valLocations) {
            /*                 echo ('<br> -> ' . $valLocations->location . ' --- ' . $valLocations->cep); */
            $location = new Location();
            $location->location = $valLocations->location;
            $location->cp = $valLocations->cep;
            $location->neighborhood = $valLocations->barrio;
            $location->street = $valLocations->calle;
            $location->shipping_cost = 0;
            $location->province_id = 51;
            $location->save();
        }
        /*         } */

        dd('cortamos');
    }
}
