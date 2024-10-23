<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailable;
use App\Jobs\SendEmailForgotPasswordJob;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    public function sendResetLinkEmail(Request $request){
        $login = $request->input($this->username());

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        switch ($field) {
            case 'name':
                $user = User::where('name',$login)->first();
                break;
            case 'email':
                $user = User::where('email',$login)->first();
                break;
        }
        /* if($field=='name'){
            $user = User::where('name',$login)->first();
        }else{
            $user = User::where('email',$login)->first();
        } */
        if(is_null($user)){
            $this->satusReturn(400,'El email no existe');
            return redirect()->back();
        }


        $codeToken = Str::random(60);
        session(['codeToken' => $codeToken, 'emailReset'=>$user->email]);
        $passwordReset = PasswordReset::where('email',$user->email)->first();
        if(is_null($passwordReset)){
           PasswordReset::create([
                'email' =>  $user->email,
                'token' =>  $codeToken,
                'created_at'    =>  date('Y-m-d H:i:s')
            ]);

        }else{
            $passwordReset->token = $codeToken;
            $passwordReset->save();
        }

        SendEmailForgotPasswordJob::dispatch($user,$codeToken);

        $this->satusReturn(200,'Se envio un email');
        return redirect()->back();


    }

    public function username(){
        return 'login';
    }


    public function passwordReset(Request $request){
        $passwordReset = PasswordReset::select('email','token')->where('email',session('emailReset'))
            ->where('token',session('codeToken'))->first();
        if(!$passwordReset) return redirect()->back();

        $email = $passwordReset->email;
        $token = $passwordReset->token;

        return view('auth.passwords.reset',compact('email','token'));

    }

    public function passwordUpdate(Request $request){

        $validate = $this->checkPassword($request->password, $request->password_confirmation);
        if(!$validate){
            $this->satusReturn(400,'Las contrasenas no coinciden');
            return redirect()->back();
        }

        $passwordReset = PasswordReset::select('email','token')->where('email',session('emailReset'))
        ->where('token',session('codeToken'))->first();
        $user = User::where('email',$passwordReset->email)->first();
        if(!$passwordReset && !$user) {
            $this->emptySession();
            $this->satusReturn(400,'Las credenciales no coinciden');
            return redirect()->back();
        }
       /*  ||  1 true  o  2 true   true
        ||  1 true  o  2 false   true
        ||  1 false  o  2 true   true
        ||  1 false  o  2 false   false

        &&  1 true  y  2 true   true
        &&  1 true  y  2 false   false
        &&  1 false  y  2 true   false
        &&  1 false  y  2 false   false */

        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset->delete();
        $this->emptySession();
        $this->satusReturn(200,'successfully');
        return redirect()->back();


    }
    protected function emptySession(){
        Session::forget(['codeToken','emailReset']);
    }
    protected function checkPassword($password, $passwodConfirmation){
        if($password != $passwodConfirmation) return false;
        return true;
    }
    protected function satusReturn($status,$message){
        if($status==200){
            Session::flash('flash_message',$message);
            Session::flash('flash_message_class','success');
        }else{
            Session::flash('flash_message',$message);
            Session::flash('flash_message_class','danger');
        }
    }
}
