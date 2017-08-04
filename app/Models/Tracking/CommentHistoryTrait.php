<?php

namespace Tracking;

use Carbon\Carbon;

trait CommentHistoryTrait
{
    /**
     * @param \BaseModel $model
     * @param $labels
     *
     * eg:
     *  $labels = [
     *      'status' => '状态',
     * ]
     */
    public static function commentHistory($labels)
    {
        self::updated(function (\BaseModel $model) use ($labels) {
            $arr = $model->getOriginal();
            $orig = clone $model;
            foreach ($arr as $k => $v) {
                $orig->setAttribute($k, $v);
            }

            $message = '';
            foreach ($labels as $field => $description) {
                if (strstr($field, '.')) {
                    $orig->load(explode('.', $field)[0]);
                    $model->load(explode('.', $field)[0]);
                }
                $old = strval(data_get($orig, $field));
                $new = strval(data_get($model, $field));
                if ($old !== $new) {
                    $message .= "$description : [{$old}] => [{$new}]\n";
                }
            }

            if ($message) {
                $model->comment($message);
            }
        });
    }

    /**
     *  根据一个字段来更新时间戳
     * @param $field
     * @param $atField
     *
     * eg:
     *      $passenger->updateTimestamp('online_status', 'online_last_at');
     *          当 online_status 改变时 online_last_at 置为 now()
     */
    public function updateTimestamp($field, $atField)
    {
        if ($this->isDirty($field)) {
            $this->$atField = Carbon::now();
        }
    }

}