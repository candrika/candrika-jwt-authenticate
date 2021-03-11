<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use Firebase\JWT\JWT;

use App\Models\User;
use App\Models\UserProfile;

class UserController extends Controller{

    public function regis(Request $request){
        
        $roles = [
            'nama_lengkap' => 'required|String',
            'username'     => 'required|email',
            'password'     => 'required|min:6',
            'alamat'       => 'required',
            'usia'         => 'required|numeric',
            'jenis_kelamin'=> 'required'
        ];

        $message = [
            'nama_lengkap.required'  => 'Nama Lengkap tidak boleh kosong',
            'username.required'      => 'Username tidak boleh kosong',
            'password.required'      => 'Password tidak boleh kosong',
            'alamat.required'        => 'Alamat tidak boleh kosong',
            'password.required'      => 'Password tidak boleh kosong',
            'usia.required'          => 'Usia tidak boleh kosong',
            'jenis_kelamin.required' => 'Jenis: Hanya boleh di isi dengan Male atau Female'
        ];

        $valid = Validator::make($request->all(),$roles,$message);

        if($valid->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$valid->errors()
            ],401);
        }

        //save user login
        $data_user = new User();
        $data_user->realname   = $request->nama_lengkap;
        $data_user->username   = $request->username;
        $data_user->password   = password_hash($this->request->input('password'), PASSWORD_BCRYPT);
        $data_user->email      = $request->username;
        $data_user->created_at = date('Y-m-d H:m:s');
        $data_user->updated_at = null;
        $data_user->save();

        //save user profile  
        $data_profile = new UserProfile();
        $data_profile->fullname   = $request->nama_lengkap;
        $data_profile->age        = $request->usia;
        $data_profile->address    = $request->alamat;
        $data_profile->sex        = $request->jenis_kelamin;
        $data_profile->created_at = date('Y-m-d H:m:s');
        $data_profile->updated_at = null;
        $data_profile->userid     = $data_user->userid;

        $data_profile->save();

        return response()->json([
            'status'=>true,
            'message'=>'User registrasi berhasil'
        ],200);
    }

    public function profileDetail(Request $request){
        
        $token = $request->header('x-token');
        $decode = JWT::decode($token, env('JWT_SECRET'), ['HS256']);

        
        $profile = DB::table('user_profile')
                   ->select('user_profile.fullname','user_profile.age','user_profile.sex','user_profile.address')
                   ->join('user','user.userid','=','user_profile.userid')
                   ->where('user_profile.userid',$decode->{'user_id'})
                   ->first();

        if(!$profile){
            return response()->json([
                'status'=>false,
                'message'=>'profile tidak ditemukan'
            ],400);
        }else{
            return response()->json([
                'status'=>true,
                'data'=>$profile
            ],200);
        }
    }

    public function updateProfile(Request $request,$profile_id){

        $roles = [
            'nama_lengkap' => 'required|String',
            'alamat'       => 'required',
            'usia'         => 'required|numeric',
            'jenis_kelamin'=> 'required'
        ];

        $message = [
            'nama_lengkap.required'  => 'Nama Lengkap tidak boleh kosong',
            'alamat.required'        => 'Alamat tidak boleh kosong',
            'usia.required'          => 'Usia tidak boleh kosong',
            'jenis_kelamin.required' => 'Jenis: Hanya boleh di isi dengan Male atau Female'
        ];
 
        $valid = Validator::make($request->all(),$roles,$message);

        if($valid->fails()){
            return response()->json([
                'status'=>false,
                'message'=>$valid->errors()
            ],400);
        }

        UserProfile::where('user_profile_id',$profile_id)->Update([
            'fullname'=>$request->nama_lengkap,
            'age'=>$request->usia,
            'sex'=>$request->jenis_kelamin,
            'address'=>$request->alamat,
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Profile berhasil di update'
        ],200);
    }
}