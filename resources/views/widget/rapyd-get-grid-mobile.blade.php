<style>
    .rapyd-grid .ibox-content:nth-child(odd) {
        background-color: #f3f6fb;
    }
</style>

@include('rapyd::toolbar', array('label'=>$label, 'buttons_right'=>$buttons['TR']))

<div class="ibox rapyd-grid" {!! $dg->buildAttributes() !!}>
    @foreach ($dg->rows as $row)
        <div class="ibox-content" {!! $row->buildAttributes() !!}>
            @foreach ($row->cells as $cell)
                <p>
                    @if ($label = $dg->columns[$cell->name]->label)
                        <strong>{{$label}}：</strong>{!! $cell->value !!}
                    @else
                        <strong>{!! $cell->value !!}</strong>
                    @endif
                </p>
            @endforeach
        </div>
    @endforeach
</div>

<div class="btn-toolbar" role="toolbar">
    @if ($dg->havePagination())
        <div class="pull-left">
            {!! $dg->links() !!}
        </div>
    @endif
</div>
