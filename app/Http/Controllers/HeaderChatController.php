<?php

namespace App\Http\Controllers;

use App\HeaderChat;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;

class HeaderChatController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HeaderChats  $headerChats
     * @return \Illuminate\Http\Response
     */
    public function show(HeaderChats $headerChats)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Hea{derChats  $headerChats
     * @return \Illuminate\Http\Response
     */
    public function edit(HeaderChats $headerChats)
    {}
        /**
         * API
         */


    public function headersResponse(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $headers = HeaderChat::where('created_by',$user->id)
                      ->orWhere('created_with',$user->id)->get();
        $allheaders = [];
        foreach($headers as $head){
            if($user === $other = User::find($head->created_with)   ){
                $name = $user->name;
            }else{
                $name = $other->name;
            }
            $avatar ='http://lorempixel.com/40/40/';
            $last_message = $head->messages()->orderBy('id','desc')->first();
            $id_header = $head->id;

            $object = [
                'name' =>$name,
                'avatar' => $avatar,
                'last_message' => $last_message->message,
                'id_header' => $id_header,
            ];
          array_push($allheaders,$object);
        }
        if(!empty($allheaders)){
            $response = [
                'success'=>true,
                'data'=>$allheaders,
            ];
        }else{
            $response = [
                'success' => false,
                'error' => 'chats_not_found',
            ];
        }
        return response()->json($response);

    }
}
