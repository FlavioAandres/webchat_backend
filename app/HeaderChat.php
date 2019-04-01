<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HeaderChat extends Model
{
    protected $table = 'header_chats';
    protected $fillable = [
        'created_by',
        'created_with',
        'is_group',
        'title',
    ];

    public function owner(){
        return $this->hasOne(App\User::class, 'id','created_by');
    }

    public function messages(){
        return $this->hasMany(Message::class, 'header_id','id');
    }

    public function groups_users(){
        return $this->hasMany(Group::class,'id_header','id');
    }


}
