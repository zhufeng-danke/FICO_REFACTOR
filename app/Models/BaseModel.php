<?php
//yubing@wutongwan.org

/*
 * 基础模型，同时也是常用功能的文档 ^_^
 */

/**
 * Class BaseModel
 *
 * @mixin Eloquent add helper function to ide-helper (eg: $obj::find() )
 *
 */
abstract class BaseModel extends Illuminate\Database\Eloquent\Model
{
    use \General\HistoryTrait;
    use \General\CommentTrait;
    use \Constants\HasEnumsTrait;
    use \Traits\HasDefaultValues;

    /**
     * doc:  https://laravel.com/docs/5.2/eloquent-mutators#attribute-casting
     *
     * Sample:
     * 'advantages' => 'array', //数组类型有点坑,详见上方文档.
     * 'is_owner' => 'boolean',
     */
    protected $casts = [];

    // The attributes that should be converted to dates, Expect the default: static::CREATED_AT, static::UPDATED_AT
    protected $dates = [];

    // If you need to disable created_at & updated_at , set this to false
    public $timestamps = true;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'laputa';

    // The human readable description of the model.
    protected $description = 'Base Model';

    /**
     * 数据保存的时候会自动验证这些规则，只有通过才能保存成功，见save()方法注释。
     *
     * doc: http://laravel.com/docs/5.2/validation#available-validation-rules
     */
    public static function rules()
    {
        return [];
    }

    /**
     * 重写此函数时, 请最后调用父类boot函数
     */
    protected static function boot()
    {
        static::observe(\ModelObserver\HistoryObserver::class, 999);
        static::observe(\ModelObserver\ValidationObserver::class, 999);

        parent::boot();
    }

    /**
     * @param $id
     * @param string $errMsg 错误信息，默认是 “找不到xxx“
     * @return static 注意,实际返回还可能是null
     * @throws ErrorMessageException
     */
    public static function findOrError($id, $errMsg = null)
    {
        if (!$model = self::find($id)) {
            throw new ErrorMessageException($errMsg ?: '找不到' . (new static)->getDescription() . ' ' . $id);
        }

        return $model;
    }

    /**
     * 示例: \Room::whereCode('100-F')->firstOrError();
     * @param $query
     * @param null $errorMsg string 错误信息，默认是 “找不到指定的xxx“
     * @return \Illuminate\Database\Eloquent\Model|static|null 注意ide-helper在这里的返回值是BaseModel,
     * @throws ErrorMessageException
     */
    public function scopeFirstOrError($query, $errorMsg = null)
    {
        /* @var BaseModel $query */
        if (!$model = $query->first()) {
            throw new ErrorMessageException($errorMsg ?: '找不到指定的' . (new static)->getDescription());
        }

        return $model;
    }

    /**
     * @param static $query
     * @param Closure $callback
     */
    public function scopeEachById($query, Closure $callback)
    {
        /** @var Collection $ids */
        $ids = $query->pluck('id');
        $ids->chunk(1000)
            ->each(function ($chunk) use ($callback) {
                static::whereIn('id', $chunk)->get()->each($callback);
            });
    }

    /*
     * 根据自身Attribute,查找和已有属性相同的数据对象,
     *  既可以视为Query Builder的另外一种快捷写法,也可以用于避免重复拼装对象
     *
     * $suite = new Suite();
     * $suite->address = '东直门内大街10号3-201';
     * if($objs = $suite->getMatchModels()) {
     *   return $objs[0];
     * } else {
     *   return $suite;
     * }
     */
    public function getMatched($attributes = [])
    {
        $builder = $this->newQuery();
        foreach ($attributes ?: array_filter($this->attributes) as $key => $value) {
            $builder = $builder->where($key, '=', $value);
        }
        return $builder->get();
    }

    public function saveIfDirty(array $options = [])
    {
        return $this->isDirty() ? $this->save($options) : true;
    }

    public function saveOrError(array $options = [])
    {
        $save = parent::saveOrFail($options);
        if (!$save) {
            $error = $this->getSavingErrors();
            $message = "{$this->getDescription()}保存失败！请检查数据格式是否正确！<br>" . ($error->first() ?? null);
            throw new ErrorMessageException($message);
        }
        return $save;
    }

    // Begin: 以下两个方法覆盖了Model类的json_encode()行为,尽量让系统中的数据更可读。
    public function toJson($options = JSON_UNESCAPED_UNICODE)
    {
        return parent::toJson($options);
    }

    public function setAttribute($key, $value)
    {
        if ($this->isJsonCastable($key) && !$this->hasSetMutator($key)) {
            $this->attributes[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            parent::setAttribute($key, $value);
        }
    }

    // End

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 测试属性是否本次被改成了特定值, `save()`调用前有效
     */
    public function isModifiedTo($attribute, $value)
    {
        return $this->isDirty($attribute) && $this->getAttribute($attribute) === $value;
    }

    /**
     * 内部流通用的链接, 定义`internal*`函数后可以用工具类`ModelTool::url($model)`等函数快速调用
     */
    public function internalLink()
    {
        return null;
    }

    /**
     * 内部流通用的标题
     */
    public function internalTitle()
    {
        return null;
    }

    /**
     * 保存失败的错误信息
     *
     * @var \Illuminate\Support\MessageBag savingErrors
     */
    private $savingErrors;

    public function setSavingErrors($errors)
    {
        $this->savingErrors = $errors;
    }

    /**
     * 读取保存失败时的错误信息
     *
     * @return \Illuminate\Support\MessageBag|null
     */
    public function getSavingErrors()
    {
        return $this->savingErrors;
    }

    /**
     * 生成一个没有id、没有时间戳、并且数据与当前Model完全相同的copy
     *
     * @return static
     */
    public function generateUnsavedCopy()
    {
        $ignoreColumns = [
            $this->getKeyName(),
            $this->getUpdatedAtColumn(),
            $this->getCreatedAtColumn(),
            'deleted_at'
        ];

        $copy = new static;
        foreach ($this->getAttributes() as $name => $value) {
            if (in_array($name, $ignoreColumns)) {
                continue;
            }

            $copy->{$name} = $value;
        }
        return $copy;
    }

    /**
     * 嵌套保存, 尝试保存已加载且修改过的Relation
     */
    public function saveNested()
    {
        foreach ($this->getRelations() as $name => $relation) {
            if ($relation instanceof BaseModel && $relation->isDirty()) {
                $relation->save();
            }

            if ($relation instanceof ArrayAccess) {
                foreach ($relation as $item) {
                    if ($item instanceof BaseModel && $item->isDirty()) {
                        $item->save();
                    }
                }
            }
        }

        if ($this->isDirty()) {
            $this->save();
        }
    }

    public function morphMany($related, $name = 'data', $type = null, $id = null, $localKey = null)
    {
        return parent::morphMany($related, $name, $type, $id, $localKey);
    }

    /**
     * $m->code . ' - ' . $m->address => $m->echo('{code} - {address}')
     *
     * @param $format
     * @return string
     */
    public function echo($format)
    {
        $open = false;
        $result = '';
        $key = null;
        foreach (str_split($format) as $char) {
            switch (true) {
                case $char === '{' && !$open:
                    $open = true;
                    $key = null;
                    break;
                case $char === '}' && $open && $key:
                    if (ends_with($key, '()')) {
                        if (str_contains($key, '.')) {
                            $parts = explode('.', $key);
                            $path = implode('.', array_slice($parts, 0, -1));
                            $method = last($parts);
                            $instance = data_get($this, $path);
                        } else {
                            $instance = $this;
                            $method = $key;
                        }
                        $method = rtrim($method, '()');
                        $result .= $instance ? call_user_func_array([$instance, $method], []) : null;
                    } else {
                        $result .= data_get($this, $key);
                    }
                    $open = false;
                    break;
                case $open:
                    $key .= $char;
                    break;
                default:
                    $result .= $char;
            }
        }
        return $result;
    }

    /**
     * 依照attributes转换成另外一个model
     * @param BaseModel|string $class
     * @return static
     */
    public function parseToModel($class)
    {
        if ($class instanceof self) {
            $class = get_class($class);
        }

        /* @var self $model */
        $model = new $class;
        return $model->newFromBuilder($this->getAttributes(), $model->getConnectionName());
    }
}
