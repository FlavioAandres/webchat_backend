<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use JWTAuth;


class ImageController extends Controller
{
    public function photoResponse($name){
        if($p = Storage::disk('avatars')->get($name)){
            $url = storage_path('profile_pic');
            return response()->file($url.'/'.$name);
        }else{
            return response()->json([
                'success' => false,
                'error' => 'Error cargando la imagen'
            ]);
        };
    }

    public function newProfilePhoto(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        if($request->file('newphoto')){
            $extension = $request->newphoto->getClientOriginalExtension();
            $time = strtotime(now());
            Storage::disk('avatars')->delete($user->path);
            $name = "$time.$extension";
            $path = $request->file('newphoto')->storeAs('',$name,'avatars');
            $user->path = $name;
            $user->save();
        }
        return response()->json([
            'success'=>true,
            'data'=>route('new_avatar',$name)
            ]);
        #return response 200 o 400
    }
}
