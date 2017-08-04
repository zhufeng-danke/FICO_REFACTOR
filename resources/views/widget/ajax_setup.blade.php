{{-- 由于系统全局启用了CSRF检查，所有的POST请求都要添加对应的token。 --}}
{{-- 通过AJAX来POST数据的地方直接@include此文件即可。 --}}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    cache: false,
    async: false,
});