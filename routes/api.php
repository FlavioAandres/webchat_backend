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

Route::group(['middleware' => ['jwtAuth','addheaders']], function () {
    Route::post('/chat/headers/','HeaderChatController@headersResponse');


    Route::post('/chat/user/header/', function(Request $request){
        $chats = App\HeaderChat::find($request->id_header)->messages;
        // return response()->json([$request->id_header]);
        return response()->json([
            'success' => true,
            'data' => $chats,
        ]);
    });


    Route::post('/chat/message/send/', function(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $chat = App\HeaderChat::find($request->idChatHeader);
        $message = [
            'message'=>$request->plainMessage,
            'sent_by'=>$user->id,
        ];
        try{
            $chat->messages()->create($message);
            $response = [
                'success'=>true,
                'data' => $message,
            ];
        }catch(Illuminate\Database\QueryExeption $e){
            $response = [
                'success'=> false,
                'error'=>$e->getMessage(),
            ];
        }catch(Exception $e){
            $response = [
                'success'=> false,
                'error'=>$e->getMessage(),
            ];
        }


        return response()->json($response);
    });

    Route::post('/chat/create', function(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $next_user = App\User::find($request->idUserToTalk);
        // return response()->json(['next_user'=>$next_user,'user'=>$user]);
        if(!is_null($user) && !is_null($next_user->email)){
            $payload = [
                'created_by'=>$user->id,
                'created_with'=>$next_user->id,
            ];
            try{
                $chat = App\HeaderChat::firstOrCreate($payload);
                $response = [
                    'success' => true,
                    'data'=> $chat,
                    'error'=>null,
                ];
            }catch(Illuminate\Database\QueryExeption $e){
                $response =[
                    'success'=>false,
                    'data' => null,
                    'error'=>$e->getMessage(),
                ];
            }
        }else{
            $response = [
                'success'=>false,
                'error'=>'usuario no encontrado',
            ];
        }

        return response()->json($response);
    });

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
