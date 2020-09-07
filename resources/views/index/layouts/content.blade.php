@section('content-header')
    <section class="content-header breadcrumbs-top mb-2 hidden">
        @if($header || $description)
            <h1 class=" float-left">
                <span class="text-capitalize">{!! $header !!}</span>
                <small>{!! $description !!}</small>
            </h1>
        @elseif($breadcrumb || config('admin.enable_default_breadcrumb'))
            <div>&nbsp;</div>
        @endif

        @include('admin::partials.breadcrumb')

    </section>
@endsection

@section('content')
    @include('admin::partials.alerts')
    @include('admin::partials.exception')
    <div class="tab-content " id="tab-content">
        {!! $content !!}
    </div>
    <script>
        //语言包
        window.refresh_current = "{{ $trans['refresh_current'] }}";
        window.open_in_new = "{{ $trans['open_in_new'] }}";
        window.open_in_pop = "{{ $trans['open_in_pop'] }}";
        window.refresh_succeeded = "{{ $trans['refresh_succeeded'] }}";


        window.use_icon = "{{ $use_icon }}" == '1';
        window.pass_urls = '{{ $pass_urls }}'.split(',');
        window.home_title = '{{ $home_title }}';
        window.home_uri = '{{ $home_uri }}';
        window.home_icon = '{{ $home_icon }}';
        window.iframes_index = '{{ $iframes_index }}';
        window.tabs_left = '{{ $tabs_left }}';
        window.bind_urls = '{{ $bind_urls }}';
        window.bind_selecter = '{{ $bind_selecter }}';

        Dcat.ready(function () {


            $('#tab-pane').height($(window).height());

            //弹出的窗口列表
            window.Pops = [];

            /**
             * 弹窗
             * @param url 地址
             * @param title 标题
             * @param area 大小
             */
            window.openPop = function (url, title, area) {
                if (!area) {
                    area = [$('#tab-content').width() + 'px', ($('#tab-content').height() - 5) + 'px'];
                }

                var index = layer.open({
                    content: url,
                    type: 2,
                    title: title,
                    anim: 2,
                    closeBtn: 1,
                    shade: false,
                    maxmin: true, //开启最大化最小化按钮
                    area: area,
                    //offset: 'rb'
                    full: function(el) {
                        $(el).find('.layui-layer-content iframe').css({'height':$(el).height()})
                    },
                    restore: function(el) {
                        $(el).find('.layui-layer-content iframe').css({'height':$(el).height()})
                    }
                });
                window.Pops.push(index);

                return index;
            }

            /**
             * 打开新iframe新页面
             * @param url 网址
             * @param title 标题
             * @param icon 图标
             * @param page_id 页面id
             * @param close 是否允许关闭
             * @param urlType 类型
             */
            window.openTab = function (url, title, icon, page_id, close, urlType) {
                if (!url) {
                    alert('url is empty.');
                    return;
                }
                if (icon) {
                    if (!/^<i/i.test(icon)) {
                        icon = '<i class="fa ' + icon + '"></i>';
                    }
                } else {
                    icon = '<i class="fa fa-file-text"></i>';
                }

                addTabs({
                    id: page_id || url.replace(/\W/g, '_'),
                    title: title || 'New page',
                    close: close != false && close != 0,
                    url: url,
                    urlType: urlType || 'absolute',
                    icon: icon
                });
            }

            //首页
            if (window == top) {
                addTabs({
                    id: '_admin_dashboard',
                    title: window.home_title,
                    close: false,
                    url: window.home_uri,
                    urlType: 'absolute',
                    icon: '<i class="fa ' + window.home_icon + '"></i>'
                });

            } else {
                location.href = window.home_uri;
                $('body').html('....');
            }

            //logo的链接点击
            // $('body').on('click', '.navbar-header a', function () {
            //     return false;
            // });

            //右上角标签操作鼠标滑出隐藏菜单
            $('#tabOptions .dropdown-menu').mouseleave(function () {
                $(this).removeClass('show');
                $(this).parent().removeClass('show');
            });

            //顶部标签容器宽度
            var visibleWidth = $('.navbar-wrapper').width() - $('.navbar-wrapper .navbar-collapse>.navbar-nav').outerWidth(true) - $('.navbar-wrapper #tabOptions').parent().outerWidth(true) - 50;
            $('.content-tabs').css({
                'left': window.tabs_left + 'px',
                'width': visibleWidth,

            });

            //更新宽高
            window.handleIframeContent = function () {
                $(".tab_iframe").css({
                    height: "100%",
                    width: "100%"
                });
            }

            //容器变化时，更改容器高度
            $(window).resize(function(){
                $('.content-wrapper,#app,#tab-content').css('height', $(window).height() - $('#pjax-container').css('padding-top').replace('px', ''));
            });

            $('.content-wrapper,#app,#tab-content').css('height', $(window).height() - $('#pjax-container').css('padding-top').replace('px', ''));
        });
    </script>
    @include('admin::partials.toastr')
@endsection

@section('app')
    {!! Dcat\Admin\Admin::asset()->styleToHtml() !!}

    <div class="content-header">
        @yield('content-header')
    </div>

    <div class="content-body" id="app">
        {{-- 页面埋点--}}
        {!! admin_section(AdminSection::APP_INNER_BEFORE) !!}

        @yield('content')

        {{-- 页面埋点--}}
        {!! admin_section(AdminSection::APP_INNER_AFTER) !!}
    </div>

    {!! Dcat\Admin\Admin::asset()->scriptToHtml() !!}
    {!! Dcat\Admin\Admin::html() !!}
@endsection

@if(! request()->pjax())
    @include('admin::layouts.page')
@else
    <title>{{ Dcat\Admin\Admin::title() }} @if($header) | {{ $header }}@endif</title>

    <script>Dcat.pjaxResponded()</script>

    {!! Dcat\Admin\Admin::asset()->cssToHtml() !!}
    {!! Dcat\Admin\Admin::asset()->jsToHtml() !!}

    @yield('app')
@endif
