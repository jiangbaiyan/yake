<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class InfoModel extends Model
{
    protected $table = 'infos';

    protected $guarded = ['id'];

    public function infoFeedbacks(){
        return $this->hasMany(InfoFeedbackModel::class,'info_id','id');
    }
}
