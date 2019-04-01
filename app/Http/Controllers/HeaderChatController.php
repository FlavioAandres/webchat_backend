<?php

namespace App\Http\Controllers;

use App\HeaderChat;
use App\User;
use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function editTitleGroup(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $headerResponse = [];
        #Data comes ok
        if(!is_null($request->id_header) && !empty($request->title)){
            try{
                $chat = HeaderChat::find($request->id_header);
                #Header Find
                if(!is_null($chat)){
                    $chat->title = $request->title;
                    $chat->save();

                    $last_message = $chat->messages()->orderBy('id','desc')->first();
                    $last_message = is_null($last_message) ? '':$last_message->message;
                    $recipents = $this->getMembersGroup($chat);
                    $headerResponse = [
                        'id_header'=>$chat->id,
                        'name'=>$chat->title,
                        'message'=>$last_message,
                        'avatar'=>'https://lorempixel.com/50/50',
                        'group'=>true,
                        'recipents'=>$recipents,
                    ];
                }
            }catch(Illuminate\Database\QueryException $e){
                return response()->json([
                    'success'=>false,
                    'error'=>$e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success'=>true,
            'data'=>$headerResponse,
        ]);
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
    public function createHeaderGroup(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        if (is_null($request->users))
            return response()->json(['success'=>false]);

        $payload =[
            'created_by'=> $user->id,
            'is_group'=>1,
            'title'=>'title',
        ];
        try{
            $header = new HeaderChat($payload);
            $header->save();


            #adding user that created group
            $ng = new Group([
                'id_header'=>$header->id,
                'id_user'=>$user->id,
            ]);
            $ng->save();

            #nexts users
            $toJoin = explode(',',$request->users);
            foreach($toJoin as $item) {
                $group = new Group([
                    'id_header'=>$header->id,
                    'id_user'=>User::find($item)->id,
                ]);

                $group->save();
            }

        }catch(Illuminate\Database\QueryException $e){
            return response()->json(['success'=>false,'error'=>$e->getMessage()]);
        }

        $recipents = $this->getMembersGroup($header);

        $headerResponse = [
            'id_header'=>$header->id,
            'name'=>$header->title,
            'message'=>'its a group',
            'avatar'=>'https://lorempixel.com/50/50',
            'group'=>true,
            'recipents'=>$recipents,
        ];

        return response()->json([
            'success'=> true,
            'data'=>    $headerResponse,
            ]);
        }

    private function getMembersGroup(HeaderChat $header){
        $members = $header->groups_users;
        $recipents = [];
        foreach($members as $item){
            $name = User::find($item->id_user)->name;
            array_push($recipents, $name);
        }
        return $recipents;
    }

    public function store(Request $request){
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
                $url = is_null($o->path) ? 'http://lorempixel.com/50/50/':route('new_avatar',$o->path);

                $object = [
                    'name' =>$name,
                    'avatar' => $url,
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

        $groups = Group::where('id_user',$user->id)->get();

        $allheaders = [];
        $int = null;

        #looking for groups

        foreach ($groups as $group) {
            $recipents = [];
            $newheader = HeaderChat::find($group->id_header);
            $last_message = $newheader->messages()->orderBy('id','desc')->first();
            $last_message = is_null($last_message) ? '':$last_message->message;

            $int = $newheader->groups_users;
            foreach($int as $item){
                $name = User::find($item->id_user)->name;
                array_push($recipents, $name);
            }


            $object = [
                'name'=> $newheader->title ? $newheader->title:'Group Without name',
                'avatar'=>'http://lorempixel.com/50/50/',
                'last_message' => $last_message,
                'id_header'=>$newheader->id,
                'group'=>true,
                'recipents'=>$recipents,
            ];
            array_push($allheaders,$object);
        }

        #Searching individuals chats

        foreach($headers as $head){
            if(!$head->is_group){
                if($user->id == $head->created_by && !is_null($head->created_with)){
                    $u = User::find($head->created_with);
                }elseif($user->id == $head->created_with){
                    $u = User::find($head->created_by);
                }
                $name = $u->name;
                $url = is_null($u->path) ? 'http://lorempixel.com/50/50/':route('new_avatar',$u->path);
                $avatar = $url;
                $last_message = $head->messages()->orderBy('id','desc')->first();
                $id_header = $head->id;
                $message = $last_message ? $last_message->message:'';

                $object = [
                    'name' =>$name,
                    'avatar' => $avatar,
                    'last_message' => $message,
                    'id_header' => $id_header,
                    'group'=>false,
                    'recipents'=>null,
                ];
              array_push($allheaders,$object);
            }
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
