<?php

namespace App\Http\Controllers;

use App\Messages;
use Illuminate\Http\Request;
use JWTAuth;
use App\User;
use App\HeaderChat;
use Pusher;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function createObjectMessage($message){

    }

    public function conversationResponse(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $chat = HeaderChat::find($request->id_header);
        $messages = $chat->messages;
        $conversation = [];
        foreach ($messages as $item) {
            $own = $user->id === $item->sent_by ?
                   true:false;


            $avatar = User::find($item->sent_by)->path;
            $url = is_null($avatar) ? 'http://lorempixel.com/40/40':route('new_avatar',$avatar);

            $object= [
                'own'=>$own,
                'message'=>$item->message,
                'created_at'=>$item->created_at,
                'id'=> $item->id,
                'avatar' => $url,
            ];

            array_push($conversation,$object);
        }
        return response()->json([
            'success' => true,
            'data' => $conversation,
        ]);
    }

    public function pusherInstance(){
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


    public function sendMessage(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        // dd($request->idChatHeader);
        $chat = HeaderChat::find($request->idChatHeader);
        $message = [
            'message'=>$request->plainMessage,
            'sent_by'=>$user->id,
        ];
        try{
            $chat->messages()->create($message);
            $conversation = [];
            $object = [];
            foreach ($chat->messages as $item) {
                $own = $user->id === $item->sent_by;
                $avatar = $user->path;

                $avatar = User::find($item->sent_by)->path;
                $url = is_null($avatar) ? 'http://lorempixel.com/40/40':route('new_avatar',$avatar);

                $object= [
                    'own'=>$own,
                    'message'=>$item->message,
                    'created_at'=>$item->created_at,
                    'id'=> $item->id,
                    'avatar' => $url,
                ];

                array_push($conversation,$object);
            }

            $response = [
                'success'=>true,
                'data' => $conversation,
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
        $pusher = $this->pusherInstance();

        #Find another user's email for send notification
        $o =null;
        if(!$chat->is_group){
            if($user->id === $chat->created_by){
                $o = User::find($chat->created_with);
            }elseif(!is_null($chat->created_with ) && $user->id === $chat->created_with){
                $o = User::find($chat->created_by);
            }
            if(!is_null($o)){
                $channel = $o->email;
                $name = $user->name;
            }
        }else{
            #If is a group
            #TODO
            $members = $chat->groups_users;
            $mailList = [];
            foreach($members as $item){
                $memObject = User::find($item->id_user);
                array_push($mailList,$memObject->email);
            }

            $name = $chat->title;
            $channel = $mailList;
            #GET OTHERS AND GIVE ARRAY
        }

        $data = [
            'type'=>'NEW_MESSAGE',
            'id_header'=>$chat->id,
            'name' => $name,

        ];
        $pusher->trigger($channel, 'my-event', $data);


        return response()->json($response);
    }

    public function index()
    {
        //
    }

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
     * @param  \App\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function show(Messages $messages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function edit(Messages $messages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Messages $messages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Messages  $messages
     * @return \Illuminate\Http\Response
     */
    public function destroy(Messages $messages)
    {
        //
    }
}
