<?php

namespace App\Http\Controllers\Api;

use App\nucooks\entities\Language;
use App\nucooks\entities\TermsCondition;
use App\nucooks\entities\UsersData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TermsController extends Controller
{
    public function terms_conditions($lang){

        if(Auth::user()){
            $languageId = Auth::user()->language_id;
            $userdata = UsersData::find(Auth::user()->id);
            if(($userdata->last_name === "") && ($userdata->name === "")){
                $user = $userdata->last_name.', '.$userdata->name;
            }else{
                $user = __('messages.users');
            }
            $image = $userdata->avatar_image;
        }else{
            if(is_null($lang)){
                $lang="pt-br";
            }
            $languageId = Language::where('lang',$lang)->first();
            $languageId = $languageId->id;
            $user = __('messages.users');
            $image = null;
        }
        if(!is_null($languageId)){
                $terms = TermsCondition::first();
                if(!is_null($terms)){
                    if($languageId == 1){
                        $text = $terms->terms_1;
                        return view('users.terms_conditions.terms',compact('text','user','image'));
                    }
                    if($languageId == 2){
                        $text = $terms->terms_2;
                        return view('users.terms_conditions.terms',compact('text','user','image'));
                    }
                    if($languageId == 3){
                        $text = $terms->terms_3;
                        return view('users.terms_conditions.terms',compact('text','user','image'));
                    }
                }

        }
        return redirect('/');
    }

/*     public function terms_conditions_portugues(){

    }
    public function terms_conditions_spanish(){

    }
    public function terms_conditions_english(){

    } */
}
