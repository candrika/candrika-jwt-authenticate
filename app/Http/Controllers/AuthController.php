<?php
namespace App\Http\Controllers;

use App\Models\User;
use Validator;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController{

    private $request;
   
    public function __construct(Request $request){
        $this->request = $request;
    }

    private function jwt(User $user){
        $paylaod=[
            'iss'=>'lumen-jwt',
            'user_id'=>$user->userid,
            'iat'=>time(),
            'exp'=>time()+60*60
        ];

        return JWT::encode($paylaod,env('JWT_SECRET'));
    }

    public function authenticate(Request $request){

        $roles = [
            'email'=>'required|String',
            'password'=>'required|String|min:6'
        ];

        $message = [
            'email.required'=>'Email tidak boleh kosong',
            'password.required'=>'Password tidak boleh kosong'
        ];

        $valid = Validator::make($request->all(),$roles,$message);
        
        if($valid->fails()){
            return response()->json(['status'=>'false','message'=>$valid->errors()],500);
        }
        
        $user = User::Where('username',$this->request->input('email'))->first();

        if(!$user){
            return response()->json([
                'status'=>'false',
                'message'=>'Email yang anda masukan salah atau belum terdaftar'
            ],400);
        }
        else{
            return response()->json([
                'status'=>true,
                'message'=>'Login berhasil',
                'token'=>$this->jwt($user)
            ],200);
        }
    }
}