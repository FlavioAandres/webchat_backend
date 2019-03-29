<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HeaderChat extends Model
{
    protected $table = 'header_chats';
    protected $fillable = [
        'created_by',
        'created_with',
    ];

    public function owner(){
        return $this->hasOne(App\User::class, 'id','created_by');
    }

    public function messages(){
        return $this->hasMany(Message::class, 'header_id','id');
    }


}
