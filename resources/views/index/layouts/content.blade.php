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
    {{--    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.js"></script>--}}
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/2.1.4/jquery.js"></script>
    <script>
        Dcat.ready(function () {


            $('#tab-pane').height($(window).height());
            //Note! You cannot use both layout-boxed and fixed at the same time. Anything else can be mixed together.
            if (!$('body').hasClass('layout-boxed')) {
                $('body').addClass('fixed'); //layout Fixed: use the class .fixed to get a fixed header and sidebar.
            }

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

            window.Pops = [];

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
                });
                window.Pops.push(index);

                return index;
            }

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

            if (!window.layer) {
                window.layer = {
                    load: function () {
                        var html = '<div style="z-index:999;margin:0 auto;position:fixed;top:90px;left:50%;" class="loading-message"><img src="/vendor/laravel-admin-ext/iframe-tabs/images/loading-spinner-grey.gif" /></div>';
                        $('.tab-content').append(html);
                        return 1;
                    },
                    close: function (index) {
                        $('.tab-content .loading-message').remove();
                    },
                    open: function () {
                        alert('layer.js dose not work.');
                    }
                };
            }

            $('body').on('click', '#tab-menu a.menu_tab', function () {
                var pageId = getPageId(this);
                var $ele = null;
                $(".sidebar-menu li a").each(function () {
                    var $meun = $(this);
                    if ($meun.attr('data-pageid') == pageId) {
                        $ele = $meun;
                        return false; //退出循环
                    }
                });
                if ($ele) {
                    $ele.parents('.treeview').not('.active').find('> a').trigger('click');
                    setTimeout(function () {
                        var $parent = $ele.parent().addClass('active');
                        $parent.siblings('.treeview.active').removeClass('active');
                        $parent.siblings().removeClass('active').find('li').removeClass('active')
                    }, 500);
                }
            });

            $('.iframe-link').off('click').on('click', function () {
                event.preventDefault();
                if ($(this).hasClass('container-refresh')) {
                    var pageId = getActivePageId();

                    var iframe = findIframeById(pageId);

                    iframe[0].contentWindow.$.admin.reload();

                    $.admin.toastr.success(refresh_succeeded, '', {positionClass: "toast-top-center"});

                    return false;
                }

                var url = $(this).attr('href');
                if (!url || url == '#' || /^javascript|\(|\)/i.test(url)) {
                    return;
                }

                if ($(this).attr('target') == '_blank') {
                    return;
                }

                if ($(this).hasClass('iframes-pass-url')) {
                    return;
                }

                if (window.pass_urls) {
                    for (var i in window.pass_urls) {
                        if (url.indexOf(window.pass_urls[i]) > -1) {
                            return true;
                        }
                    }
                }
                var icon = '<i class="fa fa-file-text"></i>';
                if ($(this).find('i.fa').length) {
                    icon = $(this).find('i.fa').prop("outerHTML");
                }
                var span = $(this).find('p');

                var path = url.replace(/^(https?:\/\/[^\/]+?)(\/.+)$/, '$2');

                var id = path == window.home_uri ? '_admin_dashboard' : path.replace(/\W/g, '_');
                addTabs({
                    id: id,
                    title: span.length ? span.text() : $(this).text().length ? $(this).text() : '*',
                    close: true,
                    url: url,
                    urlType: 'absolute',
                    icon: icon
                });


                $('.dark-mode body').addClass('dark-mode');

                $(this).attr('data-pageid', id);

                var toggle = false;
                if ($(this).parents('.dropdown').size() && (toggle = $(this).parents('.dropdown').find('.dropdown-toggle'))) {
                    toggle.trigger('click');
                }

                if ($(this).parents('.sidebar-form') && (toggle = $(this).parents('.sidebar-form').find('.input-group-btn button'))) {
                    toggle.trigger('click');
                }

                return false;
            });

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

            $('body').on('click', '.main-header a.logo', function () {
                return false;
            });

            $('.navbar-custom-menu').css('background-color', $('.main-header .navbar').css('background-color'));

            $('.navbar-custom-menu').show(); // delete it in future

            if (!$(".navbar-custom-menu>ul>*:first").hasClass('tab-options')) {
                $(".navbar-custom-menu>ul>*:first").before($('.navbar-custom-menu>ul>li.tab-options'));
            }
            var visibleWidth = $('.navbar-wrapper').width() - $('.navbar-wrapper .navbar-collapse>.navbar-nav').outerWidth(true) - $('.navbar-wrapper #tabOptions').parent().outerWidth(true) - 50;


            $('#tabOptions .dropdown-menu').mouseleave(function () {
                $(this).removeClass('show');
            });

            $('.content-tabs').css({
                'left': window.tabs_left + 'px',
                'width': visibleWidth,

            });

            setTimeout(function () {
                $('.container-refresh').off('click');
            }, 1000);

            window.handleIframeContent = function () {
                $(".tab_iframe").css({
                    height: "100%",
                    width: "100%"
                });
            }
            //
            // $('#tab-pane').height($(window).height());

            $(window).resize(function(){
                $('.content-wrapper,#app,#tab-content').css('height', $(window).height() - $('#pjax-container').css('padding-top').replace('px', ''));
            });

            $('.content-wrapper,#app,#tab-content').css('height', $(window).height() - $('#pjax-container').css('padding-top').replace('px', ''));
            // $('.content-wrapper,#app,#tab-content').css('height',$('#pjax-container').height());

            $('.dark-mode-switcher').click(function () {
                $('iframe').each(function () {
                    $(this).contents().find('body').toggleClass('dark-mode');
                })
            });

            $('.menu-toggle').click(function () {
                $('iframe').each(function () {
                    $(this).contents().find('body').toggleClass('sidebar-collapse');
                })
            });
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
