<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use JWTAuthExeption;

class UserController extends Controller
{
    /**
     * Private function for get token from database
     */
    private function getToken($email, $password){
        $token = null;
        try{
            if(!$token = JWTAuth::attempt(['email'=>$email,'password'=>$password])){
                return response()->json([
                    'response'=>'error',
                    'message' => 'this credentials are wrong',
                ]);
            }
        }catch(JWTAuthExeption $e){
            return response()->json([
                'response'=>'error',
                'message'=>'Token creation failed',
            ]);
        }
        return $token;
    }
    /**
     * Login for use from front-end
     */

    public function login(Request $request){
        $user = User::where('email',$request->email)->get()->first();
        $pass = \Hash::check($request->password, $user->password);
        $headers = \App\HeaderChat::where('created_by',$user->id)->orWhere('created_with',$user->id)->get();

        if ($user && $pass){
            $token = $this->getToken($request->email,$request->password);
            $user->auth_token = $token;
            $user->save();
            $resp = [
                'success'=>true,
                'data'=>[
                    'id' => $user->id,
                    'auth_token'=>$user->auth_token,
                    'name'=>$user->name,
                    'email'=>$user->email,
                    'avatar'=>'http://lorempixel.com/50/50',
                ],
            ];
        }else{
           $resp = [
            'success' => false,
            'data' => 'credentials not found'
           ];
        }
        return response()->json($resp, 201);
    }

    public function register(Request $request){
        $data = [
            'name'=>$request->name,
            'email' => $request->email,
            'password'=> \Hash::make($request->password),
            'auth_token' => '',
        ];
        $user = new User($data);
        try{
            if($user->save()){
                $token = $this->getToken($request->email, $request->password);
                if(!is_string($token)){
                    return response()->json([
                        'success'=>false,
                        'data'=>'Something is wrong with generation token process',
                    ]);
                }
                $user->auth_token = $token;
                $user->save() ;
                $resp = [
                    'success'=>true,
                    'data'=>[
                        'id' => $user->id,
                        'auth_token'=>$user->auth_token,
                        'name'=>$user->name,
                        'email'=>$user->email
                        ]
                    ];
                }else{
                    $resp= [
                        'success'=>false,
                        'data'=>'Something is wrong with generation token process',
                    ];
            }
        }catch(\Illuminate\Database\QueryException $e){
            $resp= [
                'success'=>false,
                'data'=>'Something is wrong saving this record',
                'error'=>$e->getMessage(),
            ];
        }
        return response()->json($resp, 201);
    }
}
