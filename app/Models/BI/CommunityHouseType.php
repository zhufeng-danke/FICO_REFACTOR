<?php

namespace App\Models\BI;

use Illuminate\Database\Eloquent\Model;

class CommunityHouseType extends Model
{
    protected $connection = 'forecast';
    protected $table = 'community_house_type';
    protected $description = '小区户型库';
    public $timestamps = false;

}
