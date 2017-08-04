<?php // zhangwei@wutongwan.org

use Illuminate\Support\HtmlString;

class HTMLWidget
{
    const BLANK = '&nbsp;';

    // eg: <span class="label label-primary">现房</span>
    public static function label($text, $style = 'default', $attributes = [])
    {
        $attributes ['class'] = 'label label-' . $style;
        return new HtmlString('<span' . Html::attributes($attributes) . ">{$text}</span>");
    }

    public static function text($text, $style = 'success', $attributes = [])
    {
        $attributes['class'] = array_get($attributes, 'class', '') . ' text-' . $style;

        return new HtmlString('<span' . Html::attributes($attributes) . ">{$text}</span>");
    }

    /**
     * 按照 rapyd 风格添加字段
     *
     * @param $field
     * @param $label
     */
    public static function field($field, $label, $type = 'text', $value = '', $options = [], $readonly = true)
    {
        $attr = [
            'id' => $field,
            'class' => 'form-control',
            'name' => $field,
            'value' => $value,
            'min' => 0
        ];

        if (!$readonly) {
            $type === 'select' ? array_push($attr, 'disabled') : array_push($attr, 'readonly');
        }

        if ($type === 'select') {
            $form = Form::select($field, $options, $value, $attr);
        } else {
            $form = Form::$type($label, $value, $attr);
        }
        $text = "<div class='form-group clearfix' id='fg_{$field}'>
            <label for='{$field}' class='col-sm-2 control-label'>{$label}：</label>
            <div class='col-sm-10' id='div_{$field}'>
                {$form}
            </div>
        </div>";
        return new HtmlString($text);
    }

    public static function fieldSrcLink()
    {
        return Form::hidden('src_link', Request::header('referer'));
    }

    /**
     * 对象数组转换成表格，每个对象占一行，属性作为表头
     *
     * @param array $data            第一维表示rows，首个元素的keys作为表头
     * @param array $class           表示table的class属性
     * @param Closure|null $callable 对每个cell进行自定义计算
     * @param bool $hasTitle         是否不设有表头
     * @return string
     */
    public static function table($data, $class = [], \Closure $callable = null, $hasTitle = false)
    {
        $html = "<table class='table table-striped " . join(' ', $class) . "'>";

        foreach ($data as $rowId => $row) {
            $html .= "<tr>";

            //  head
            if (!$hasTitle) {
                foreach ($row as $field => $val) {
                    $html .= "<th class='text-center'>{$field}</th>";
                }
                $html .= "</tr><tr>";
                $hasTitle = true;
            }

            //  body
            foreach ($row as $field => $val) {
                $html .= "<td class='text-center'>" . (is_callable($callable) ? $callable($val) : $val) . "</td>";
            }

            $html .= "</tr>";
        }
        $html .= "</table>";
        return new HtmlString($html);
    }

    /**
     * 换行快捷函数
     * @param array $lines
     * @param bool|false $paragraph 用P标签换行
     * @return HtmlString
     */
    public static function lines(array $lines, $paragraph = false)
    {
        if ($paragraph) {
            return new HtmlString('<p>' . join("</p>\n<p>", $lines) . '</p>');
        }

        return new HtmlString(join('<br>', $lines));
    }

    /**
     * 把二维数组按行列展开成表格
     * @param array $lines
     * @return string
     */
    public static function linesToTable(array $lines)
    {
        $arr = [];
        foreach ($lines as $line) {
            $obj = [];
            foreach ($line as $idx => $value) {
                $obj[$idx] = $value;
            }
            $arr [] = $obj;
        }
        return HTMLWidget::table($arr);
    }

    /**
     * \n 换行变更为 <br> 换行
     * @param $text
     * @param bool|false $paragraph
     * @return string
     */
    public static function text2lines($text, $paragraph = false)
    {
        return new HtmlString(self::lines(explode("\n", $text), $paragraph));
    }

    /**
     * Font Awesome Icon
     *
     * @param string $style 样式, eg: pencil (不带`fa-`前缀)
     * @return HtmlString
     */
    public static function fa($style)
    {
        return Html::tag('i', '', ['class' => 'fa fa-' . $style]);
    }

    public static function unescape($html)
    {
        return new HtmlString($html);
    }

    public static function buttons($buttons, $attr = [])
    {
        $html = '';
        foreach ((array)$buttons as $title => $href) {
            $html .= link_to($href, $title, $attr) . ' ';
        }
        return $html;
    }
}
