<?php // zhangwei@wutongwan.org

/**
 * Wrapper for Rapyd DataEdit Class
 *
 * 增加的特性:
 *  1. 导出功能的快捷操作函数
 *  2. view函数, 更多拓展的可能性
 *
 * @mixin \Zofe\Rapyd\DataForm\DataForm
 */
class DataTable extends Zofe\Rapyd\DataGrid\DataGrid
{
    private $allowExport;
    private $onExport;

    /**
     * 是否添加导出功能
     *
     * @param Closure|bool $condition
     * @return $this
     */
    public function export($condition = true)
    {
        $this->allowExport = value($condition);

        return $this;
    }

    /**
     * 导出前执行的回调, 可以对导出时的数据做些特殊处理
     *
     * @param Closure $callback
     * @return $this
     */
    public function onExport(\Closure $callback)
    {
        $this->onExport = $callback;

        return $this;
    }

    public function isExporting()
    {
        return $this->allowExport && Input::get('__rapyd_export');
    }

    public function build($view = '')
    {
        if ($this->allowExport) {
            $this->link(Input::fullUrlWithQuery(['__rapyd_export' => 'yes']), '导出', 'TR');
        }

        return parent::build($view);
    }

    /**
     * @param null $view
     * @param array $data
     * @param array $mergeData
     * @return Redirect|View
     */
    public function view($view = null, $data = [], $mergeData = [])
    {
        if ($this->isExporting()) {
            if ($this->onExport && is_callable($this->onExport)) {
                call_user_func($this->onExport);
            }

            return $this->buildExcel('导出结果');
        }

        return view($view, $data, $mergeData);
    }
}
