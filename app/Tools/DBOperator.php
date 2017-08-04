<?php

/**
 * 不使用ORM操作数据库时的工具类
 */
trait DBOperator
{
    /**
     * @param $table
     * @param $id
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getRow($table, $id)
    {
        return \DB::table($table)->where(['id' => $id]);
    }

    protected function tableName($modelClass)
    {
        /** @var BaseModel $model */
        $model = new $modelClass;
        return $model->getTable();
    }
}
