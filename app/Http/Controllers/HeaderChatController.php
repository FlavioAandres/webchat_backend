<?php

namespace App\Http\Controllers;

use App\HeaderChat;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Pusher;

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
    private function getPusherInstance(){
        $options = array(
            'cluster' => 'us2',
            'useTLS' => true
            );
            $pusher = new Pusher(
            'afd3371eb6832adcdd07',
            '0b89c4dbd598fab11633',
            '748140',
            $options
            );

            return $pusher;
    }
    private function createHeaderGroup(Request $request){
        return response()->json('hola');
    }

    public function store(Request $request)
    {
        if($request->isGroup){
            $this->createHeaderGroup($request);
        }

        $user = JWTAuth::parseToken()->authenticate();
        $next_user = User::find($request->idUserToTalk);
        // return response()->json(['next_user'=>$next_user,'user'=>$user]);
        if(!is_null($user) && !is_null($next_user) && !is_null($next_user->email)){
            $payload = [
                'created_by'=>$user->id,
                'created_with'=>$next_user->id,
            ];
            try{
                $chat = HeaderChat::firstOrCreate($payload);
                $avatar ='http://lorempixel.com/40/40/';
                $message = '';
                $id_header = $chat->id;

                #Search name
                if($user->id == $chat->created_by){
                    $u = User::find($chat->created_with);
                    $o = User::find($chat->created_by);
                }elseif($user->id == $head->created_with){
                    $u = User::find($chat->created_by);
                    $o = User::find($chat->created_with);
                }

                $name = $u->name;
                $channel = $o->email;

                $object = [
                    'name' =>$name,
                    'avatar' => $avatar,
                    'last_message' => $message,
                    'id_header' => $id_header,
                ];
                $response = [
                    'success' => true,
                    'data'=> $object,
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

        $pusher = $this->getPusherInstance();
        $data = [
            'type'=>'NEW_HEADER',
            'id_header'=>$chat->id,
            'name' => $name,
        ];
        $pusher->trigger($channel, 'my-event', $data);

        return response()->json($response);
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
            # If user starting session is the message owner
            if($user->id == $head->created_by){
                $name = User::find($head->created_with)->name;
            }elseif($user->id == $head->created_with){
                $name = User::find($head->created_by)->name;
            }
            $avatar ='http://lorempixel.com/40/40/';
            $last_message = $head->messages()->orderBy('id','desc')->first();
            $id_header = $head->id;
            $message = $last_message ? $last_message->message:'';

            $object = [
                'name' =>$name,
                'avatar' => $avatar,
                'last_message' => $message,
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
