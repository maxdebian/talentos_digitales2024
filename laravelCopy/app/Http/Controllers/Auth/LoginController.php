<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function login(Request $request){


    //eloquent ORM
    //Entidad::Metodo
        //Select * from users where id=2
    //$user = User::find(2);
        //Select * from users
    //$user = User::all();
    //$id=1;
    //Select * from users where id=1
    //$user = User::where('id',$id)->get();
    //Select * from users where id=1 limit 1
/*     $user = User::where('id',$id)->first();
    $name='admin';
    $user = User::where('name',$name)->where('id',$id)->first();

    dd($user); */






        $userLocal=null;
        $credentials = $this->credentials($request);

        if(Auth::validate($credentials)){
            $user = Auth::getLastAttempted();
            auth()->login($user);
            return redirect('home');
        }else{
            if(!empty($credentials['name'])){
                $userLocal = User::where('name',$credentials['name'])->first();
            }else if(!empty($credentials['email'])){
                $userLocal = User::where('email',$credentials['email'])->first();
            }
            if(is_null($userLocal)){
                Session::flash('flash_message','The User is wrong');
                Session::flash('flash_message_class','danger');
            }else{
                Session::flash('flash_message','The password is wrong');
                Session::flash('flash_message_class','danger');
            }
            return back()->withInput();
        }
    }

    public function credentials(Request $request){
        $login = $request->input($this->username());
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        return [
            $field      =>  $login,
            'password'  =>  $request->input('password'),
        ];
    }

    public function username(){
        return 'login';
    }





}
