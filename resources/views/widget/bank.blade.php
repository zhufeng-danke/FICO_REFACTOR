<div class="ibox">
    <div class="ibox-title"><h3>银行信息录入</h3></div>
    <div class="ibox-content">
        <form class="form-horizontal" method="POST" id="bank-entry">
            @foreach($form as $label => $field)
                <div class="form-group">
                    <label class="col-sm-2 control-label">
                        <span class="text-danger">*</span> {{ $label }}
                    </label>
                    <div class="col-sm-10">
                        {{ $field }}
                    </div>
                </div>
            @endforeach
            @if($note)
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <pre>{{ $note }}</pre>
                    </div>
                </div>
            @endif
            @if(!$readonly)
                {{ csrf_field() }}
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default">保存</button>
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>

@if(!$readonly)
    <script>
        $(document).ready(function () {
            var $entry = $('#bank-entry');
            var url = location.pathname + '?ajax=city-list&bank-id=';

            $entry.find('[name=bank]').on('change', function () {
                var bankId = $(this).val();
                if (!bankId) {
                    return null;
                }
                $.getJSON(url + bankId, function (cities) {
                    var $city = $entry.find('[name=city]');
                    $city.find('option').remove();
                    $.each(cities, function (i, item) {
                        $city.append($('<option>', {
                            value: item,
                            text: item
                        }));
                    });
                });
            });
        });

        $(document).ready(function() {
            $("select.form-control").select2({
                language: "zh-CN",
                theme: "bootstrap"
            });
        });
    </script>
@endif
