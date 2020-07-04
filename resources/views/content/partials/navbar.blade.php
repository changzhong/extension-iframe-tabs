
<nav class="header-navbar navbar-expand-lg navbar hidden
    navbar-with-menu {{ $configData['navbar_class'] }}
    {{ $configData['navbar_color'] }}
        navbar-light navbar-shadow " style="top: 0;">

    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav">
                    <li class="nav-item mr-auto">
                        <a class="nav-link menu-toggle" data-widget="pushmenu" style="cursor: pointer">
                            <i class="text-primary feather icon-disc"></i>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="navbar-collapse">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    {!! Dcat\Admin\Admin::navbar()->render('left') !!}
                </div>
                <div class="float-right d-flex align-items-center">
                    {!! Dcat\Admin\Admin::navbar()->render() !!}
                </div>
                <ul class="nav navbar-nav float-right">
                    {{--User Account Menu--}}
                    {!! admin_section(AdminSection::NAVBAR_USER_PANEL) !!}

                    {!! admin_section(AdminSection::NAVBAR_AFTER_USER_PANEL) !!}
                </ul>
            </div>
        </div>
    </div>
</nav>

{{-- Search Start Here --}}
<ul class="main-search-list-defaultlist d-none">

</ul>
<ul class="main-search-list-defaultlist-other-list d-none">
    <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer">
        <a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start"><span class="mr-75 feather icon-alert-circle"></span><span>No
results found.</span></div>
        </a>
    </li>
</ul>
<script>
    $('.menu-toggle').on('click', function () {
        $(this).find('i').toggleClass('icon-circle icon-disc')
    })

    $('#tab-pane').height($(window).height());

    const theme = localStorage.getItem('dcat-admin-theme-mode');
    if(theme === 'dark') {
        $('body').addClass('dark-mode');
    }


    Dcat.ready(function(){
        // console.log('reade');
        setTimeout(function(){
            $('.tree-quick-edit, .dialog-create').off('click').click(function () {
            url = $(this).data('url');
            location.href = url;
            // return ;
            // $.ajax({
            //     url: url+'?_dialog_form_=1',
            //     success: function (template) {
            //
            //             layer.open({
            //                 type: 1,
            //                 title: 'aaa',
            //                 shadeClose: true,
            //                 shade: 0.3,
            //                 offset: "20%",
            //                 shadeClose : false,
            //                 area: ['700px', '670px'],
            //                 content: template//传入一个链接地址 比如：http://www.baidu.com
            //             });
            //         }
            //     })
            });
        }, 500);

    })
</script>
