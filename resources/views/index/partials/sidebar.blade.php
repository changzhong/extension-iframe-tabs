<div class="main-menu">
    <div class="main-menu-content">
        <aside class="main-sidebar {{ $configData['sidebar_style'] }} shadow">
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
                @if(cache('last_update_time'))
                    <div class="async-div bg-cyan" style="margin-bottom: 10px">上次同步数据时间
                        <br><b id="last_update_time">{!! date('Y-m-d H:i', cache('last_update_time')) !!}</b>
                    </div>
                @endif

                <div class="search-menu-container">
                    <input type="text" placeholder="菜单搜索" class="form-control" onchange="searchMenu()" id="searchInput">
                    <a class="clear-search hidden" title="清空" onclick="clearSearch()" href="javascript:;">×</a>
                </div>
                <div class="search-menu">

                </div>

                <ul class="nav nav-pills nav-sidebar flex-column all-menu" data-widget="treeview" style="padding-top: 10px">
                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_TOP) !!}

                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU) !!}

                    {!! admin_section(AdminSection::LEFT_SIDEBAR_MENU_BOTTOM) !!}
                </ul>
            </div>
        </aside>
    </div>
</div>

