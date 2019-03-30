<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function changeProfilePicture(Request $request){
        #find user id
        if($request->file('newphoto')){
            #Set time() name
            #To send to storage/profile_ṕic
            #
            #route saved on $user->path = route
        }
        #return response 200 o 400 
    }
}
