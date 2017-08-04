<?php // zhangwei@wutongwan.org

use Illuminate\Support\HtmlString;

class ModelTool
{
    /**
     * 生成 $model 某个属性的标签元素
     * eg:
     *  // $room->status = '可出租'
     *  label($room, 'status') return <span class="label label-success">可出租</span>
     *
     * @param BaseModel $model
     * @param string $attribute 属性名
     * @param array $attributes 放到标签元素上的其他属性
     * @return HtmlString|null
     */
    public static function label($model, $attribute, $attributes = [])
    {
        if (!$model instanceof BaseModel) {
            return null;
        }

        if (!$value = $model->getAttribute($attribute)) {
            return null;
        }

        return HTMLWidget::label($value, self::style($model, $attribute), $attributes);
    }

    public static function labels($model, array $labels)
    {
        $result = [];
        foreach ($labels as $label) {
            $result []= self::label($model, $label);
        }
        return join(HTMLWidget::BLANK, $result);
    }

    /**
     * 根据配置文件拿到 $model 属性对应的 $style
     *
     * eg: style($room, 'status')
     */
    public static function style($model, $attr)
    {
        if (!$model instanceof BaseModel) {
            return null;
        }

        $default = config('model-style.default');
        if (!$value = $model->getAttribute($attr)) {
            return $default;
        }

        return self::getStyle(get_class($model), $attr, $value) ?? $default;
    }

    /**
     * 根据类名、属性、值, 从配置文件中拿到样式
     */
    private static function getStyle($className, $attr, $value)
    {
        $styles = config('model-style.model.' . $className . '.' . $attr);

        if (is_array($styles)) {
            return array_get($styles, $value);
        }

        // widget机制
        if (is_string($styles)) {
            if ($widget = config('model-style.widget.' . $styles)) {
                return array_get($widget, $value);
            }

            return $styles;
        }

        // 继承机制
        if ($base = config('model-style.model.' . $className . '.__extend')) {
            return self::getStyle($base, $attr, $value);
        }

        return null;
    }

    public static function url(BaseModel $model = null)
    {
        if (!$model || !$model->getKey()) {
            return null;
        }

        if (!method_exists($model, 'internalLink')) {
            return null;
        }

        return $model->internalLink();
    }

    public static function title(BaseModel $model = null)
    {
        if (!$model || !$model->getKey()) {
            return null;
        }

        if (!method_exists($model, 'internalTitle')) {
            return null;
        }

        return $model->internalTitle();
    }

    public static function href(BaseModel $model = null, $newBlank = true)
    {
        if (!$model) {
            return null;
        }

        list($url, $text) = [self::url($model), self::title($model)];

        if (!$url) {
            if (!$text) {
                return null;
            }
            return $text;
        }

        if (!$newBlank) {
            return link_to($url, $text);
        }

        return link_to_blank($url, $text);
    }

    /**
     * 列出所有Model
     *
     * @return array eg: ["Human", "Area", ...]
     */
    public static function listModel()
    {
        static $models;
        if (!$models) {
            $models = classes_in(app_path('Models'), BaseModel::class);
        }
        return $models;
    }

    /**
     * 根据表名获取对应的Model
     *
     * @param string $tableName    数据表名
     * @param null $connectionName 连接名, 默认见 config('database.default')
     * @return mixed|null
     */
    public static function getModelByTable($tableName, $connectionName = null)
    {
        $connectionName = $connectionName ?: config('database.default');
        foreach (self::listModel() as $modelName) {
            /** @var BaseModel $model */
            $model = new $modelName;
            if ($model->getTable() === $tableName && $model->getConnectionName() === $connectionName) {
                return $modelName;
            }
        }
        return null;
    }

    /**
     * 审批 - button 控件, 注释详见下面view
     */
    public static function approval(BaseModel $model, $name, $attributes = [])
    {
        return HTMLWidget::unescape(
            view('admin.approval.button', compact('model', 'name', 'attributes'))
        );
    }

    public static function approvalPanel(BaseModel $model, $name)
    {
        return HTMLWidget::unescape(
            view('admin.approval.panel', compact('model', 'name'))
        );
    }

    /**
     * @param BaseModel|\General\ApprovalTrait $model
     * @param $name
     * @param null $text
     * @param array $attributes
     * @return string
     */
    public static function requestApproval(BaseModel $model, $name, $text = null, $attributes = [])
    {
        $approval = $model->getApproval($name);
        if (!$approval->pendingStatus() || $approval->isLocked()) {
            return null;
        }

        if (!isset($attributes['class'])) {
            $attributes ['class'] = 'btn btn-default btn-sm';
        }

        if ($approval->hasRequestApproval()) {
            $text = '已申请' . $approval->description();
            $attributes ['disabled'] = 'disabled';
            $link = '#';
        } else {
            $link = action('Admin\General\ApprovalController@getRequestApprove', [
                $model->getTable(),
                $model->getKey(),
                $name
            ]);
        }

        return link_to($link, $text ?: '申请' . $approval->description(), $attributes);
    }

    /**
     * 历史记录列表链接
     *
     * @param BaseModel $model
     * @return string
     */
    public static function historyLink(BaseModel $model)
    {
        return action('Admin\General\HistoryController@getIndex') . '?' . http_build_query([
            'table_name' => $model->getTable(),
            'data_id' => $model->getKey(),
            'search' => 1
        ]);
    }

    public static function tableName($modelClass)
    {
        assert(is_subclass_of($modelClass, BaseModel::class));

        /** @var BaseModel $model */
        $model = new $modelClass;

        return $model->getTable();
    }
}
