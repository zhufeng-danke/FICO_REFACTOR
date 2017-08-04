{{--

@include('widget.charts.line',[
    'title'=>'图标标题',
    'lines'=> [
        [
            'name'=> '第一条线',
            'data'=>[
                'x1' => 1,
                'x2' => 2,
            ],
        ],
    ],
])

--}}

<?
/* @var array $lines */
$xs = array_keys($lines[0]['data']);
$legents = array_column($lines, 'name');
$series = [];
foreach ($lines as $line) {
    $series [] = [
        'name' => $line['name'],
        'type' => 'line',
        'stack' => $line['name'],
        'data' => array_values($line['data']),
    ];
}
?>


<div id="{{md5($title)}}" style="width:100%;height:100%;">

</div>

@js('bower/echarts/dist/echarts.min.js')
<script type="text/javascript">
    $(window).ready(function () {
        var myChart = echarts.init(document.getElementById('{{md5($title)}}'));
        var option = {
            title: {
                text: '{{$title}}'
            },
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: {!! json_encode($legents, JSON_UNESCAPED_UNICODE ) !!}
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: {!! json_encode($xs, JSON_UNESCAPED_UNICODE) !!}
            },
            yAxis: {
                type: 'value'
            },
            series: {!! json_encode($series, JSON_UNESCAPED_UNICODE) !!}
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    });
</script>