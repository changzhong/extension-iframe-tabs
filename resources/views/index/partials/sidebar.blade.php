<div class="main-menu">
    <div class="main-menu-content">
        <aside class="main-sidebar {{ $configData['sidebar_dark'] ? 'sidebar-dark-white' : 'sidebar-light-primary' }} shadow">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mr-auto">
                        <a href="{{ admin_url('/') }}" class="navbar-brand waves-effect waves-light">
                            <span class="logo-mini">{!! config('admin.logo-mini') !!}</span>
                            <span class="logo-lg">{!! config('admin.logo') !!}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar pb-3">
                <div class="">
                    <input type="text" placeholder="菜单搜索" class="form-control" onchange="searchMenu(this)">
                </div>
                <ul class="nav nav-pills nav-sidebar flex-column search-menu" data-widget="treeview" style="padding-top: 10px">

                </ul>
                <ul class="nav nav-pills nav-sidebar flex-column all-menu" data-widget="treeview" style="padding-top: 10px">
                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_TOP) !!}

                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU) !!}

                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_BOTTOM) !!}
                </ul>
            </div>
        </aside>
    </div>
</div>

<script>
    function searchMenu(self) {
        var text = $(self).val();
        $('.search-menu').html('');
        if(text.length > 0) {
            $('.all-menu').addClass('hidden');
            $(".all-menu li a.iframe-link").each(function () {
                // console.log($(this).text().replace(/(^\s*)|(\s*$)/g, ""));
                if($(this).text().replace(/(^\s*)|(\s*$)/g, "").indexOf(text) != -1) {
                    $(this).addClass('nav-link');
                    $('.search-menu').append('<li class="nav-item">'+$(this).parent().html()+'</li>');
                }
            })


            $('.iframe-link').off('click').on('click', function() {
                event.preventDefault();
                var url = $(this).attr('href');
                if (!url || url == '#' || /^javascript|\(|\)/i.test(url)) {
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

                //更改菜单为选中
                $('.nav-sidebar').find('a.active').removeClass('active');
                $(this).addClass('active');

                return false;
            });
        } else {
            $('.all-menu').removeClass('hidden');
        }
    }
</script>
