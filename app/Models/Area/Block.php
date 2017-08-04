<?php namespace Area;

class Block extends AbstractArea
{
    const LEVEL_NAME = \Area::LEVEL_商圈;

    public function district()
    {
        return $this->belongsTo(District::class, 'parent_id');
    }

    public function passenger()
    {
        $this->belongsToMany(\Passenger::class, 'passenger_blocks', 'block_id', 'passenger_id');
    }

    public function team()
    {
        return $this->belongsToMany(\CorpDepartment::class, 'coterie_block_teams', 'block_id', 'team_id');
    }
}