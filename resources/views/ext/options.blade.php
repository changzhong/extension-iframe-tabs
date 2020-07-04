<ul class="nav navbar-nav float-right" id="tabOptions">

<li class="dropdown dropdown-user nav-item">
    <a href="#" class="dropdown-toggle nav-link dropdown-user-link" data-toggle="dropdown">
        {{ $trans['oprations'] }}<i class="fa fa-caret-down" style="padding-left: 3px;"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-right">
        <li><a class="dropdown-item tabReload" href="javascript:;" onclick="refreshTab();">{{ $trans['refresh_current'] }}</a></li>
        <li><a class="dropdown-item tabCloseCurrent" href="javascript:;" onclick="closeCurrentTab();">{{ $trans['close_current'] }}</a></li>
        <li><a class="dropdown-item tabCloseAll" href="javascript:;" onclick="closeOtherTabs(true);">{{ $trans['close_all'] }}</a></li>
        <li><a class="dropdown-item tabCloseOther" href="javascript:;" onclick="closeOtherTabs();">{{ $trans['close_other'] }}</a></li>
        <li><a class="dropdown-item tabscrollLeft" href="javascript:;" onclick="scrollTabLeft();">{{ $trans['scroll_left'] }}</a></li>
        <li><a class="dropdown-item tabscrollRight" href="javascript:;" onclick="scrollTabRight();">{{ $trans['scroll_right'] }}</a></li>
        <li><a class="dropdown-item tabscrollRight" href="javascript:;" onclick="scrollTabCurrent();">{{ $trans['scroll_current'] }}</a></li>
    </ul>
</li>
</ul>
