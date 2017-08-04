<?php

use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Doc => Eloquent: Relations => Custom Polymorphic Types
 *
 * Laputa 多个通用表中的关联都是根据 表名 + 主键 ，此 trait 会注册如下数组到 morph map
 *
 *  [Room::class => 'rooms', ...]
 *
 * Class RelationMorphTableToModel
 */
trait RelationMorphTableToModel
{
    protected static function bootRelationMorphTableToModel()
    {
        Relation::morphMap(array_values(\ModelTool::listModel()));
    }
}