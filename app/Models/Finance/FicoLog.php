<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FicoLog extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $connection = 'fico'; //数据库配置
    protected $table = 'fico_logs';
    protected $description = '日志表';

    public static function inputLog($type = '',$data_id, $action, $content)
    {
        $log_obj = new self();
        $log_obj->related_doc_type = $type;
        $log_obj->related_doc_id = $data_id;
        $log_obj->action = $action;
        $log_obj->content = $content;
        $log_obj->creator = \CorpAuth::id();
        $log_obj->save();
    }

}
