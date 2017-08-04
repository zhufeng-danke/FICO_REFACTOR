<?php namespace Traits;

trait HasDefaultValues
{
    /**
     * 新建记录时的默认值
     *
     * @var array
     */
    protected $defaults = [];

    public static function bootHasDefaultValues()
    {
        static::creating(function (\BaseModel $model) {
            foreach ($model->getDefaults() as $column => $value) {
                $model->setAttribute($column, $value);
            }
        });
    }

    public function getDefaults()
    {
        return $this->defaults;
    }
}
