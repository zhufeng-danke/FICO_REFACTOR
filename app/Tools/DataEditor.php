<?php
//yubing@wutongwan.org

/**
 * Wrapper for Rapyd DataEdit Class
 * 增加的特性:
 * 1. addType的方法都能自动提示出来了。
 * 2. 支持Model的rules()定义，避免重复定义。[ 详见ApplyRules() ]
 * 3. 自定义了view。 [ 详见 data_editor.blade.php ]
 * 4. 增加常量定义。
 */
class DataEditor extends \Zofe\Rapyd\DataEdit\DataEdit
{

    const STATUS_IDLE = 'idle';
    const STATUS_SHOW = 'show';
    const STATUS_CREATE = 'create';
    const STATUS_MODIFY = 'modify';
    const STATUS_DELETE = 'delete';
    const STATUS_NOT_FOUND = 'unknow_record';

    const MODE_READONLY = 'readonly';
    const MODE_EDITABLE = 'editable';

    public function addReadonlyText($label, $text = ' ')
    {
        return $this->addText(md5(microtime() . $text), $label)
            ->insertValue($text)->showValue($text)->updateValue($text)->mode('readonly');
    }

    private function applyRules()
    {
        $rules = with($this->model)->rules();
        foreach ($this->fields as $field) {
            if ($rule = array_get($rules, $field->db_name)) {
                if ($field->mode == self::MODE_EDITABLE) {
                    $field->rule($rule);
                }
            }
        }
    }

    // 之前添加的字段均为必填项
    public function aboveRequired()
    {
        if (!in_array($this->status, [self::STATUS_CREATE, self::STATUS_MODIFY])) {
            return;
        }

        // 上面字段为必填项
        /** @var \Zofe\Rapyd\DataForm\Field\Field $field */
        foreach ($this->fields as $field) {
            if ($field->mode != 'editable') {
                continue;
            }
            if ($field->required) {
                continue;
            }

            $field->label = '* ' . $field->label;
            $field->rule('required');
        }
    }

    public function readonly($condition = true)
    {
        if (value($condition)) {
            $this->status = self::STATUS_SHOW;
        }

        return $this;
    }

    public function build($view = '')
    {
        if ($this->status === self::STATUS_DELETE) {
            throw new ErrorMessageException('禁止删除！！');
        }

        $view = 'data_editor';
        \Rapyd::style('span > label { font-size: 1.2em; }');
        $this->applyRules();
        parent::build($view);
    }

    /**
     * @param string $field
     * @param string $label
     * @param null $placeholder
     * @param bool $opts 要不要走selectOpts, 默认为true
     * @return \Zofe\Rapyd\DataForm\Field\Select
     *
     * @example
     *      $edit = DataEditor::source(new Room)
     *
     *      $edit->addModelChoice('status', '状态', '选择状态');
     *      相当于
     *      $edit->addSelect('status', '状态')->options(selectOpts(Room::listStatus(), '选择状态'));
     *
     * @todo addSelect还有个第三个参数
     */
    public function addModelChoice(string $field, string $label, $placeholder = null, $opts = true)
    {
        $class = get_class($this->model);

        //  优先使用listXXX, 如果没有结果, 就尝试constants
        $funcName = 'list' . ucfirst(camel_case($field));
        $list = method_exists($class, $funcName)
            ? call_user_func_array([$class, $funcName], [])
            : $class::constants(strtoupper($field));

        $field = $this->addSelect($field, $label);
        if ($opts) {
            $field->options(selectOpts($list, $placeholder));
        }
        return $field;
    }

    //记录了所有Filed的分组情况
    private $groups = [];

    /**
     * @param string $name Group Name
     * @param Closure $closure
     */
    public function group($name, Closure $closure)
    {
        $list = $this->fields;
        call_user_func($closure);
        $new_added_fields = array_diff_key($this->fields, $list);
        $this->groups[$name] = array_merge($this->getGroup($name), $new_added_fields);

        return $this;
    }

    public function getGroup($name)
    {
        return $this->groups[$name] ?? [];
    }

    //将一组字段标记为Readonly
    //@todo 感觉最好还是放到build的时候执行,避免冲突.
    public function setReadonlyMode($group_name, $condition = true)
    {
        if (!$condition) {
            return;
        }

        foreach ($this->getGroup($group_name) as $filed) {
            $filed->mode('readonly');
        }
    }
}