<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    protected $fillable = ['id_header','id_user'];
    public function header(){
        return $this->hasOne(App\HeaderChat::class, 'id','id_header');
    }
}
