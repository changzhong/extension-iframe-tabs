/*
Thanks to https://github.com/bswsfhcw/AdminLTE-With-Iframe
*/

//保存页面id的field
var pageIdField = "data-pageId";
var load_index;

/**
 * 获取页面的id
 * @param element
 * @returns {jQuery}
 */
function getPageId(element) {
    if (element instanceof jQuery) {
        return element.attr(pageIdField);
    } else {
        return $(element).attr(pageIdField);
    }
}

/**
 * 获取对应的页签
 * @param pageId
 * @returns {null}
 */
function findTabTitle(pageId) {
    var $ele = null;
    $(".page-tabs-content").find("a.menu_tab").each(function () {
        var $a = $(this);
        if ($a.attr(pageIdField) == pageId) {
            $ele = $a;
            return false; //退出循环
        }
    });
    return $ele;
}

/**
 * 根据页面ID获取对应的pane
 * @param pageId
 * @returns {null}
 */
function findTabPanel(pageId) {
    var $ele = null;
    $("#tab-content").find("div.tab-pane").each(function () {
        var $div = $(this);
        if ($div.attr(pageIdField) == pageId) {
            $ele = $div;
            return false; //退出循环
        }
    });
    return $ele;
}

/**
 * 根据pageId查找对应的iframe
 * @param pageId
 * @returns {*}
 */
function findIframeById(pageId) {
    return findTabPanel(pageId).children("iframe");
}

/**
 * 获取当前选中的页签
 * @returns {jQuery}
 */
function getActivePageId() {
    var $a = $('.page-tabs-content').find('.active');
    return getPageId($a);
}

function canRemoveTab(pageId) {
    return findTabTitle(pageId).find('.page_tab_close').size() > 0;
}

//添加tab
var addTabs = function (options) {
    var defaultTabOptions = {
        id: Math.random() * 200,
        urlType: "relative",
        title: "New page",
        icon: ''
    };

    options = $.extend(true, defaultTabOptions, options);

    if (options.urlType === "relative") {
        // var url = window.location.protocol + '//' + window.location.host + "/";
        var basePath = window.location.pathname + "/../";
        options.url = basePath + options.url;
    }

    var pageId = options.id;
    if (window.use_icon) {
        options.title = options.icon + options.title;
    } else {
        options.title = '<i class="fa fa-fa-ban" style="visibility:hiden;" ></i>' + options.title;
    }

    //判断这个id的tab是否已经存在,不存在就新建一个
    if (findTabPanel(pageId) === null) {

        //创建新TAB的title
        // title = '<a  id="tab_' + pageId + '"  data-id="' + pageId + '"  class="menu_tab" >';

        var $title = $('<a href="javascript:void(0);"></a>').attr(pageIdField, pageId).addClass("menu_tab");

        var $text = $("<span class='page_tab_title'></span>").html(options.title).appendTo($title);
        // title += '<span class="page_tab_title">' + options.title + '</span>';

        //是否允许关闭
        if (options.close) {
            var $i = $("<i class='fa fa-times-circle page_tab_close' style='cursor: pointer' onclick='closeTab(this);'></i>").attr(pageIdField, pageId).appendTo($title);
            // title += ' <i class="fa fa-times-circle page_tab_close" style="cursor: pointer;" data-id="' + pageId + '" onclick="closeTab(this)"></i>';
        }

        //加入TABS
        $(".page-tabs-content").append($title);


        var $tabPanel = $('<div role="tabpanel" class="tab-pane"></div>').attr(pageIdField, pageId);

        if (options.content) {
            //是否指定TAB内容
            $tabPanel.append(options.content);
        } else {
            //没有内容，使用IFRAME打开链接
            if (!load_index) {
                load_index = layer.load(0, {
                    shade: false,
                    time: 6000
                }); //0代表加载的风格，支持0-2
            }

            if (options.url.indexOf("?") !== -1) {
                options.url += '&iframe=1'
            } else {
                options.url += '?iframe=1'
            }

            console.log('options.url', options.url);
            var $iframe = $("<iframe></iframe>").attr("src", options.url).css({
                "width": "100%",
                "height": "100%"
            }).attr("frameborder", "no").attr("id", "iframe_" + pageId).addClass("tab_iframe").attr(pageIdField, pageId);
            //frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="yes"  allowtransparency="yes"

            //iframe 加载完成事件

            $iframe.load(function () {
                layer.close(load_index);
                load_index = 0;
            });

            $tabPanel.append($iframe);

        }

        // $tab = $(content);
        $("#tab-content").append($tabPanel);
    }

    activeTabByPageId(pageId);

};

//关闭tab
var closeTab = function (item) {
    //item可以是a标签,也可以是i标签
    //它们都有data-id属性,获取完成之后就没事了
    var pageId = getPageId(item);
    closeTabByPageId(pageId);
};

function getViewPort() {
    var e = window,
        a = 'inner';
    if (!('innerWidth' in window)) {
        a = 'client';
        e = document.documentElement || document.body;
    }

    return {
        width: e[a + 'Width'],
        height: e[a + 'Height']
    };
}

function closeTabByPageId(pageId) {
    var $title = findTabTitle(pageId); //有tab的标题
    var $tabPanel = findTabPanel(pageId); //装有iframe

    if ($title.hasClass("active")) {
        //要关闭的tab处于活动状态
        //要把active class传递给其它tab

        //优先传递给后面的tab,没有的话就传递给前一个
        var $nextTitle = $title.next();
        var activePageId;
        if ($nextTitle.size() > 0) {
            activePageId = getPageId($nextTitle);
        } else {
            activePageId = getPageId($title.prev());
        }

        setTimeout(function () {
            //某种bug，需要延迟执行
            activeTabByPageId(activePageId);
        }, 100);

    } else {
        //要关闭的tab不处于活动状态
        //直接移除就可以了,不用传active class

    }

    $title.remove();
    $tabPanel.remove();
    // scrollToTab($('.menu_tab.active')[0]);

}

function closeTabOnly(pageId) {
    var $title = findTabTitle(pageId); //有tab的标题
    var $tabPanel = findTabPanel(pageId); //装有iframe
    $title.remove();
    $tabPanel.remove();
}

var closeCurrentTab = function () {
    var pageId = getActivePageId();
    if (canRemoveTab(pageId)) {
        closeTabByPageId(pageId);
    }
};

function refreshTabById(pageId, force) {
    var $iframe = findIframeById(pageId);
    var url = $iframe.attr('src');

    if (/^https?:/.test(url) && url.indexOf(top.document.domain) < 0) {
        $iframe.attr("src", url); // 跨域状况下，重新设置url 刷新原始地址
    } else {
        $f = $iframe[0];
        if (force) {
            $iframe.attr("src", url); //强制刷新到原始地址
        } else {
            $f.contentWindow.location.reload(true); //带参数刷新 当前页面 , url 可能与原始的有变化
        }
    }
    if (!load_index) {
        load_index = layer.load(0, {
            shade: false,
            time: 6000
        }); //0代表加载的风格，支持0-2
    }
}

var refreshTab = function () {
    //刷新当前tab
    var pageId = getActivePageId();
    refreshTabById(pageId);
};

function getTabUrlById(pageId) {
    var $iframe = findIframeById(pageId);
    return $iframe[0].contentWindow.location.href;
}

function getTabUrl(element) {
    var pageId = getPageId(element);
    getTabUrlById(pageId);
}


/**
 * 编辑tab的标题
 * @param pageId
 * @param title
 */
function editTabTitle(pageId, title) {
    var $title = findTabTitle(pageId); //有tab的标题
    var $span = $title.children("span.page_tab_title");
    $span.text(title);
}

//计算多个jq对象的宽度和
var calSumWidth = function (element) {
    var width = 0;
    $(element).each(function () {
        width += $(this).width();
    });
    return width;
};
//滚动到指定选项卡
var scrollToTab = function (element) {
    // console.log('element', element);
    //element是tab(a标签),装有标题那个
    //div.content-tabs > div.page-tabs-content
    var marginLeftVal = calSumWidth($(element).prevAll()), //前面所有tab的总宽度
        marginRightVal = calSumWidth($(element).nextAll()), //后面所有tab的总宽度
        visibleWidth = $('.content-tabs').outerWidth(), //tab(a标签)显示区域的总宽度
        scrollVal = 0, //将要滚动的长度
        tabWidth = $(".page-tabs-content").outerWidth(),
        selfWidth = $(element).outerWidth(true); //自己的宽度

    // console.log('窗口宽度', visibleWidth, 'tab宽度', tabWidth)
    var nextWidth = $(element).next().size() ? $(element).next().outerWidth(true) : 0;
    if (tabWidth < visibleWidth) {
        //所有的tab都可以显示的情况
        // console.log('全部显示')
        scrollVal = 0;
    } else if (marginRightVal <= (visibleWidth - selfWidth - nextWidth)) {
        // console.log('向右滚动')
        //向右滚动
        //marginRightVal(后面所有tab的总宽度)小于可视区域-(当前tab和下一个tab的宽度)
        if ((visibleWidth - $(element).next().outerWidth(true)) > marginRightVal) {
            scrollVal = marginLeftVal;
            var tabElement = element;
            while ((scrollVal - $(tabElement).outerWidth()) > ($(".page-tabs-content").outerWidth() - visibleWidth)) {
                scrollVal -= $(tabElement).prev().outerWidth();
                tabElement = $(tabElement).prev();
            }
        }
    } else if (marginLeftVal > (visibleWidth - selfWidth - nextWidth)) {
        // console.log('向左滚动', (visibleWidth - $(element).outerWidth(true) - $(element).prev().outerWidth(true)))
        //向左滚动
        scrollVal = marginLeftVal - (visibleWidth - selfWidth - nextWidth);
    }
    //执行动画
    $('.page-tabs-content').animate({
        marginLeft: 0 - scrollVal + 'px'
    }, "fast");
};

//滚动条滚动到左边
var scrollTabLeft = function () {
    var marginLeftVal = Math.abs(parseInt($('.page-tabs-content').css('margin-left')));
    var visibleWidth = $(window).width() - $('.main-sidebar').outerWidth(true) - $('.navbar-wrapper .navbar-collapse>.navbar-nav').outerWidth(true) - $('.navbar-wrapper #tabOptions').parent().outerWidth(true) - 50;
    var scrollVal = 0;
    if ($(".page-tabs-content").width() < visibleWidth) {
        return false;
    } else {
        var tabElement = $(".menu_tab:first");
        var offsetVal = 0;
        while ((offsetVal + $(tabElement).outerWidth(true)) <= marginLeftVal) {
            offsetVal += $(tabElement).outerWidth(true);
            tabElement = $(tabElement).next();
        }
        offsetVal = 0;
        if (calSumWidth($(tabElement).prevAll()) > visibleWidth) {
            while ((offsetVal + $(tabElement).outerWidth(true)) < (visibleWidth) && tabElement.length > 0) {
                offsetVal += $(tabElement).outerWidth(true);
                tabElement = $(tabElement).prev();
            }
            scrollVal = calSumWidth($(tabElement).prevAll());
        }
    }
    $('.page-tabs-content').animate({
        marginLeft: 0 - scrollVal + 'px'
    }, "fast");
};
//滚动条滚动到右边
var scrollTabRight = function () {
    var visibleWidth = $('.content-tabs').outerWidth(), //tab(a标签)显示区域的总宽度
        scrollVal = 0, //将要滚动的长度
        tabWidth = $(".page-tabs-content").outerWidth();

    if (tabWidth < visibleWidth) {
        return false;
    } else {
        scrollVal = tabWidth - visibleWidth;
        if (scrollVal > 0) {
            $('.page-tabs-content').animate({
                marginLeft: 0 - scrollVal + 'px'
            }, "fast");
        }
    }
};

//关闭其他选项卡
var closeOtherTabs = function (isAll) {
    if (isAll) {
        //关闭全部
        $('.page-tabs-content').children("[" + pageIdField + "]").find('.page_tab_close').parents('a').each(function () {
            var $a = $(this);
            var pageId = getPageId($a);
            closeTabOnly(pageId);

            // closeTab($a);
            /*$('#' + $(this).data('id')).remove();
             $(this).remove();*/
        });
        var firstChild = $(".page-tabs-content").children().eq(0); //选中那些删不掉的第一个菜单
        if (firstChild) {
            //激活这个选项卡
            activeTabByPageId(getPageId(firstChild));

            /*$('#' + firstChild.data('id')).addClass('active');
             firstChild.addClass('active');*/
        }
    } else {
        //除此之外全部删除
        $('.page-tabs-content').children("[" + pageIdField + "]").find('.page_tab_close').parents('a').not(".active").each(function () {
            var $a = $(this);
            var pageId = getPageId($a);
            closeTabOnly(pageId);
            // closeTab($a);
            /*$('#' + $(this).data('id')).remove();
             $(this).remove();*/
        });
        var firstChild = $(".page-tabs-content").children().eq(0); //选中那些删不掉的第一个菜单
        if (firstChild) {
            //激活这个选项卡
            scrollToTab(firstChild);
            /*$('#' + firstChild.data('id')).addClass('active');
             firstChild.addClass('active');*/
        }
    }
};

function scrollTabCurrent() {
    scrollToTab($('.page-tabs-content').find('.active'));
}

//激活Tab,通过id
function activeTabByPageId(pageId) {
    $(".menu_tab").removeClass("active");
    $("#tab-content").find(".active").removeClass("active");
    //激活TAB
    var $title = findTabTitle(pageId).addClass('active');
    findTabPanel(pageId).addClass("active");
    // scrollToTab($('.menu_tab.active'));
    scrollToTab($title[0]);

    var titel_text = $('head title').text();

    if (/\|/.test(titel_text)) {
        titel_text = titel_text.replace(/\|.+$/, '| ' + $title.text());
    } else {
        titel_text += ' | ' + $title.text();
    }
    $('head title').text(titel_text);
}


/*
 * Context.js
 * Copyright Jacob Kelley
 * MIT License
 *
 * Modified by Joshua Christman
 */

context = (function () {

    var options = {
        fadeSpeed: 100,
        filter: function ($obj) {
            // Modify $obj, Do not return
        },
        above: 'auto',
        left: 'auto',
        preventDoubleContext: true,
        compress: false
    };

    function initialize(opts) {

        options = $.extend({}, options, opts);

        //隐藏页签上面的菜单
        $(document).on('click', function () {
            $('.dropdown-context').fadeOut(options.fadeSpeed, function () {
                $('.dropdown-context').css({
                    display: ''
                }).find('.drop-left').removeClass('drop-left');
            });
        });


        if (options.preventDoubleContext) {
            $(document).on('contextmenu', '.dropdown-context', function (e) {
                e.preventDefault();
            });
        }

        $(document).on('mouseenter', '.dropdown-submenu', function () {
            var $sub = $(this).find('.dropdown-context-sub:first'),
                subWidth = $sub.width(),
                subLeft = $sub.offset().left,
                collision = (subWidth + subLeft) > window.innerWidth;
            if (collision) {
                $sub.addClass('drop-left');
            }
        });

    }

    function updateOptions(opts) {
        options = $.extend({}, options, opts);
    }

    //构建页签菜单
    function buildMenu(data, id, subMenu) {
        var subClass = (subMenu) ? ' dropdown-context-sub' : '',
            compressed = options.compress ? ' compressed-context' : '',
            $menu = $('<ul class="dropdown-menu dropdown-context' + subClass + compressed + '" id="dropdown-' + id + '"></ul>');

        return buildMenuItems($menu, data, id, subMenu);
    }

    //构建页签之元素
    function buildMenuItems($menu, data, id, subMenu, addDynamicTag) {
        var linkTarget = '';
        for (var i = 0; i < data.length; i++) {
            if (typeof data[i].divider !== 'undefined') {
                var divider = '<li class="divider';
                divider += (addDynamicTag) ? ' dynamic-menu-item' : '';
                divider += '"></li>';
                $menu.append(divider);
            } else if (typeof data[i].header !== 'undefined') {
                var header = '<li class="nav-header';
                header += (addDynamicTag) ? ' dynamic-menu-item' : '';
                header += '">' + data[i].header + '</li>';
                $menu.append(header);
            } else if (typeof data[i].menu_item_src !== 'undefined') {
                var funcName;
                if (typeof data[i].menu_item_src === 'function') {
                    if (data[i].menu_item_src.name === "") { // The function is declared like "foo = function() {}"
                        for (var globalVar in window) {
                            if (data[i].menu_item_src == window[globalVar]) {
                                funcName = globalVar;
                                break;
                            }
                        }
                    } else {
                        funcName = data[i].menu_item_src.name;
                    }
                } else {
                    funcName = data[i].menu_item_src;
                }
                $menu.append('<li class="dynamic-menu-src" data-src="' + funcName + '"></li>');
            } else {
                if (typeof data[i].href == 'undefined') {
                    data[i].href = '#';
                }
                if (typeof data[i].target !== 'undefined') {
                    linkTarget = ' target="' + data[i].target + '"';
                }
                if (typeof data[i].subMenu !== 'undefined') {
                    var sub_menu = '<li class="dropdown-submenu';
                    sub_menu += (addDynamicTag) ? ' dynamic-menu-item' : '';
                    sub_menu += '"><a tabindex="-1" class="dropdown-item" href="' + data[i].href + '">' + data[i].text + '</a></li>'
                    $sub = (sub_menu);
                } else {
                    var element = '<li';
                    element += (addDynamicTag) ? ' class="dynamic-menu-item"' : '';
                    element += '><a tabindex="-1" class="dropdown-item" href="' + data[i].href + '"' + linkTarget + '>';
                    if (typeof data[i].icon !== 'undefined')
                        element += '<span class="glyphicon ' + data[i].icon + '"></span> ';
                    element += data[i].text + '</a></li>';
                    $sub = $(element);
                }
                if (typeof data[i].action !== 'undefined') {
                    $action = data[i].action;
                    $sub
                        .find('a')
                        .addClass('context-event')
                        .on('click', createCallback($action));
                }
                $menu.append($sub);
                if (typeof data[i].subMenu != 'undefined') {
                    var subMenuData = buildMenu(data[i].subMenu, id, true);
                    $menu.find('li:last').append(subMenuData);
                }
            }
            if (typeof options.filter == 'function') {
                options.filter($menu.find('li:last'));
            }
        }
        return $menu;
    }

    function addContext(selector, data) {
        if (typeof data.id !== 'undefined' && typeof data.data !== 'undefined') {
            var id = data.id;
            $menu = $('body').find('#dropdown-' + id)[0];
            if (typeof $menu === 'undefined') {
                $menu = buildMenu(data.data, id);
                $('body').append($menu);
            }
        } else {
            var d = new Date(),
                id = d.getTime(),
                $menu = buildMenu(data, id);
            $('body').append($menu);
        }

        //右键事件
        $(selector).on('contextmenu', function (e) {
            e.preventDefault();
            e.stopPropagation();

            rightClickEvent = e;
            currentContextSelector = $(this);

            $('.dropdown-context:not(.dropdown-context-sub)').hide();

            $dd = $('#dropdown-' + id);

            $dd.find('.dynamic-menu-item').remove(); // Destroy any old dynamic menu items
            $dd.find('.dynamic-menu-src').each(function (idx, element) {
                var menuItems = window[$(element).data('src')]($(selector));
                $parentMenu = $(element).closest('.dropdown-menu.dropdown-context');
                $parentMenu = buildMenuItems($parentMenu, menuItems, id, undefined, true);
            });

            if (typeof options.above == 'boolean' && options.above) {
                $dd.addClass('dropdown-context-up').css({
                    top: e.pageY - 20 - $('#dropdown-' + id).height(),
                    left: e.pageX - 13
                }).fadeIn(options.fadeSpeed);
            } else if (typeof options.above == 'string' && options.above == 'auto') {
                $dd.removeClass('dropdown-context-up');
                var autoH = $dd.height() + 12;
                if ((e.pageY + autoH) > $('html').height()) {
                    $dd.addClass('dropdown-context-up').css({
                        top: e.pageY - 20 - autoH,
                        left: e.pageX - 13
                    }).fadeIn(options.fadeSpeed);
                } else {
                    $dd.css({
                        top: e.pageY + 10,
                        left: e.pageX - 13
                    }).fadeIn(options.fadeSpeed);
                }
            }

            if (typeof options.left == 'boolean' && options.left) {
                $dd.addClass('dropdown-context-left').css({
                    left: e.pageX - $dd.width()
                }).fadeIn(options.fadeSpeed);
            } else if (typeof options.left == 'string' && options.left == 'auto') {
                $dd.removeClass('dropdown-context-left');
                var autoL = $dd.width() - 12;
                if ((e.pageX + autoL) > $('html').width()) {
                    $dd.addClass('dropdown-context-left').css({
                        left: e.pageX - $dd.width() + 13
                    });
                }
            }
        });
    }

    function destroyContext(selector) {
        $(document).off('contextmenu', selector).off('click', '.context-event');
    }

    return {
        init: initialize,
        settings: updateOptions,
        attach: addContext,
        destroy: destroyContext
    };
})();


var createCallback = function (func) {
    return function (event) {
        func(event, currentContextSelector, rightClickEvent)
    };
};

Dcat.ready(function () {
    //左边菜单点击事件
    $('.pop-link').off('click').on('click', function () {
        event.preventDefault();
        var url = $(this).attr('href');
        if (url) {
            var windowWidth = window.innerWidth;
            var popWidth = windowWidth < 1200 ? '80%' : '1000px';
            var title = $(this).text();
            top.openPop(url, title, [popWidth, '600px'])
        }
        return false;
    });

    //左边菜单点击事件
    $('.iframe-link').off('click').on('click', function () {
        event.preventDefault();
        var url = $(this).attr('href');
        if (!url || url == '#' || /^javascript|\(|\)/i.test(url)) {
            return;
        }

        var icon = '<i class="fa feather icon-circle"></i>';
        if ($(this).find('i.fa').length) {
            icon = $(this).find('i.fa').prop("outerHTML");
        }
        var span = $(this).find('p');

        var path = url.replace(/^(https?:\/\/[^\/]+?)(\/.+)$/, '$2');

        var id = path == window.home_uri ? '_admin_dashboard' : path.replace(/\W/g, '_');

        var title = span.length ? span.text() : (
            $(this).text().replace(/(^\s*)|(\s*$)/g, "").length > 0 ? $(this).text() : (
                $(this).attr('title') ? $(this).attr('title'): '新页面'
            )
        );

        top.addTabs({
            id: id,
            title: title,
            close: true,
            url: url,
            urlType: 'absolute',
            icon: icon
        });
        return false;
    });
});

$(function () {
    var $tabs = $(".menuTabs");
    //点击选项卡的时候就激活tab
    $tabs.on("click", ".menu_tab", function () {
        var pageId = getPageId(this);
        activeTabByPageId(pageId);
    });

    //双击选项卡刷新页面
    $tabs.on("dblclick", ".menu_tab", function () {
        // console.log("dbclick");
        var pageId = getPageId(this);
        refreshTabById(pageId, true);
    });

    //选项卡右键菜单
    function findTabElement(target) {
        var $ele = $(target);
        if (!$ele.is("a")) {
            $ele = $ele.parents("a.menu_tab");
        }
        return $ele;
    }

    context.init({
        preventDoubleContext: false, //不禁用原始右键菜单
        compress: true //元素更少的padding
    });

    var windowWidth = window.innerWidth;
    var popWidth = windowWidth < 1200 ? '80%' : '1000px';
    context.attach('.page-tabs-content', [
        //            {header: 'Options'},
        {
            text: window.refresh_current,
            action: function (e, $selector, rightClickEvent) {
                //e是点击菜单的事件
                //$selector就是＄（".page-tabs-content")
                //rightClickEvent就是右键打开菜单的事件

                var pageId = getPageId(findTabElement(rightClickEvent.target));
                refreshTabById(pageId);

            }
        }, {
            text: window.open_in_pop,
            action: function (e, $selector, rightClickEvent) {
                var pageId = getPageId(findTabElement(rightClickEvent.target));
                var url = getTabUrlById(pageId);
                var title = findTabTitle(pageId);
                title = title ? title.text() : false;
                window.openPop(url, title, [popWidth, '600px']);
            }
        },
        {
            text: window.open_in_new,
            action: function (e, $selector, rightClickEvent) {
                var pageId = getPageId(findTabElement(rightClickEvent.target));
                var url = getTabUrlById(pageId);
                window.open(url);

            }
        }
    ]);
});


function clearSearch() {
    $('#searchInput').val('');
    searchMenu();
}

function searchMenu() {
    var text = $('#searchInput').val();
    $('.search-menu').html('');
    if(text.length > 0) {
        $('.all-menu').addClass('hidden');
        $('.clear-search').removeClass('hidden');
        var str = '<ul class=" flex-column nav nav-pills nav-sidebar" data-widget="treeview" style="padding-top: 10px">';

        $(".all-menu li a.iframe-link").each(function () {
            // console.log($(this).text().replace(/(^\s*)|(\s*$)/g, ""));
            if($(this).text().replace(/(^\s*)|(\s*$)/g, "").indexOf(text) != -1) {
                $(this).addClass('nav-link');
                str += '<li class="nav-item">'+$(this).parent().html()+'</li>';
            }
        })

        str += '</ul>';
        console.log('str', str);
        $('.search-menu').html(str).removeClass('hidden');


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
        $('.clear-search').addClass('hidden');
        $('.search-menu').html('');
        $('.all-menu').removeClass('hidden');
    }
}
