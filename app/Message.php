<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $fillable = ['message','sent_by'];

    public function header(){
        return $this->belongsTo(App\HeaderChat::class,'id','id_header');
    }

    public function sentBy(){
        return $this->belongsTo(App\User::class, 'id','id_message');
    }
}
