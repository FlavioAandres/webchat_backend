<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/get/image/{name}','ImageController@photoResponse');

Route::group(['middleware' => ['jwtAuth','addheaders']], function () {
    Route::post('/user/profile/image','ImageController@newProfilePhoto');
    Route::post('/chat/headers/','HeaderChatController@headersResponse');

    Route::post('/search/users',function(Request $request){
        $users =  DB::table('users')->where('name','like',"%$request->userLike%")->get();

        $collection = [];
        foreach ($users as $item) {
            // return response()->json(['users'=>$users]);
            $obj = [
                'name' => $item->name,
                'id' => $item->id,
            ];
            array_push($collection,$obj);
        }

        $response = [
            'success'=>true,
            'data' => $collection,
        ];

        return response()->json($response);
    });

    Route::post('/chat/user/header/', 'MessageController@conversationResponse');
    Route::post('/chat/message/send/', 'MessageController@sendMessage');
    Route::post('/chat/create', 'HeaderChatController@store');
    Route::get('/users/list/', function(){
        $users = App\User::all();
        $resp = [
            'success'=> true,
            'data'=>$users,
        ];
        return response()->json($resp, 201);
    });
});

//Some request comes without token like login and register request
//api-header middleware give token

Route::group(['middleware' => 'addheaders'], function () {
    Route::post('user/login','UserController@login');
    Route::post('user/reg','UserController@register');
});
