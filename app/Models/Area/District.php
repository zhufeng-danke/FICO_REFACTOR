<?php namespace Area;

class District extends AbstractArea
{
    const LEVEL_NAME = \Area::LEVEL_行政区;

    public function blocks()
    {
        return $this->hasMany(Block::class, 'parent_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'parent_id');
    }
}