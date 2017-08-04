<?php namespace Forecast;

/**
 * Forecast\HouseStateTicket
 *
 * @property integer $id
 * @property string $date_created
 * @property integer $house_id
 * @property integer $owner_id
 * @property string $final_status
 * @property integer $household_id
 * @property string $household_reserve
 * @property string $realtime_status
 * @property string $track_status
 * @mixin \Eloquent
 */
class HouseStateTicket extends \BaseModel
{
    protected $connection = 'forecast';
    protected $table = 'house_state_ticket';
}
