{{-- Rapyd Javascripts --}}

{!!  Rapyd::scripts() !!}

{{-- 移动端使用默认的日期控件 --}}
@if( UserAgent::isMobile() )
    <script>
        jQuery.fn.datepicker = function (opts) {
            if (this.selector.indexOf('.input-daterange') !== -1) {
                $(this).find('input')
                        .attr('type', 'date')
                        .each(function () {
                            $(this).parent().before(($(this).attr('placeholder') + '：'));
                        })
            } else {
                $(this).attr('type', 'date');
                if ($(this).parents('.form-inline').size()) {
                    $(this).before(($(this).attr('placeholder') + '：'));
                }
            }
        };

        //  移动端使用datetime-local, 提交前需要format一下格式
        jQuery.fn.datetimepicker = function (opts) {
            if ($(this).val()){
                $(this).val($(this).val().replace(' ', 'T'));
                $(this).attr('type', 'datetime-local');
            }
        };
        $('form').on('submit', function () {
            $('input[type=datetime-local]').each(function () {
                $(this).attr('type', 'text');
                $(this).val($(this).val().replace('T', ' ').substr(0, 16) + ':00');
            });
        });

        // 移动端会自动弹数字键盘
        $('input[type=number]').each(function (idx, ele) {
            $(ele).attr('type', 'tel')
        })
    </script>
@endif
