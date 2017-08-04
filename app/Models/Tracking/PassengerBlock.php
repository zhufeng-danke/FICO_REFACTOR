<?php

/**
 * PassengerBlock    客户咨询商圈
 *
 * @property integer $id
 * @property integer $passenger_id
 * @property integer $block_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PassengerBlock extends BaseModel
{
    protected $description = '客源-咨询商圈表';
    protected $table = 'passenger_blocks';

    public function block()
    {
        return $this->belongsTo(\Area\Block::class);
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }
}
