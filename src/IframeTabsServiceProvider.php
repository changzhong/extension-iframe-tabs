<?php

namespace Dcat\Admin\Extension\IframeTabs;

use Dcat\Admin\Admin;
use Dcat\Admin\Extension\IframeTabs\Middleware\ForceLogin;
use http\Env\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class IframeTabsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $extension = IframeTabs::make();

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, IframeTabs::NAME);
        }

        if ($lang = $extension->lang()) {
            $this->loadTranslationsFrom($lang, IframeTabs::NAME);
        }

        $this->app->booted(function () use ($extension) {
            $extension->routes(__DIR__.'/../routes/web.php');
        });

        $assetPath = 'vendors/dcat-admin-extensions/iframe-tabs';

        //生成静态文件
        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path($assetPath)],
                'iframe-tabs'
            );
        }

        //加载路由
        $this->app->booted(function () use($extension) {
           $extension->routes(__DIR__ . '/../routes/web.php');
        });


        //加载js
        Admin::booting(function () use($assetPath){
            Admin::js($assetPath.'/bootstrap-tab.js');
            Admin::js($assetPath.'/extends.js');
        });

        if ($this->inWeb()) {

            Admin::booted(function () use ($assetPath, $extension) {
                if (\Request::route()->getName() == 'iframes.index') {
                    //首页
                    \View::prependNamespace('admin', __DIR__ . '/../resources/views/index');

                    Admin::css($extension->config('tabs_css', $assetPath.'/dashboard.css'));

                    Admin::js($assetPath.'/jquery-2.1.4.js');
                } else {
        

                    $this->initSubPage();

                    //更改布局
                    \View::prependNamespace('admin', __DIR__ . '/../resources/views/content');
                    $this->contentScript();
                    Admin::css($assetPath.'/content.css');
                }
            });
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        app('router')->aliasMiddleware('iframe.login', ForceLogin::class);
    }

    //判断是否在浏览器还是命令行
    protected function inWeb()
    {
        $c = request('c', '');

        return !$this->app->runningInConsole()
            && (!$c || !preg_match('/.*?admin:minify.*?/i', $c)); // if run admin:minify in `admin/helpers/terminal/artisan`
    }


    //初始化子页面, 添加回调等信息
    protected function initSubPage()
    {
        if (!in_array((new IframeTabs())->config('bind_urls', 'none'), ['new_tab', 'popup'])) {
            return;
        }

        $method = strtolower(request()->method());
        $session = request()->session();

        if ($method == 'get') {
            $_ifraem_id_ = $session->pull('_ifraem_id_', '');
            $after_save = $session->pull('after_save', '');
            if ($_ifraem_id_ && $session->has('toastr')) {

                if ($session->has('toastr')) {
                    $toastr = $session->get('toastr');
                    $type = Arr::get($toastr->get('type'), 0, 'success');
                    $message = Arr::get($toastr->get('message'), 0, '');

                    if ($type == 'success') {
                        $session->put('_list_ifraem_id_', $_ifraem_id_);
                        $session->put('_list_after_save_', $after_save);
                        $session->put('_success_message_', $message);
                    }
                }
            }
        } else if ($method == 'put' || $method == 'post') {

            $post_ifraem_id_ = request()->input('_ifraem_id_', '');

            $post_after_save = request()->input('after-save', '');

            if ($post_ifraem_id_) {
                $session->put('_ifraem_id_', $post_ifraem_id_);
            } else {
                $session->forget('_ifraem_id_');
            }

            if ($post_after_save) {
                $session->put('after_save', $post_after_save);
            } else {
                $session->forget('after_save');
            }
        }
    }

    protected function contentScript()
    {
        $session = request()->session();

        $_pjax = request()->input('_pjax', '');

        $_ifraem_id_ = request()->input('_ifraem_id_', '');
        $_list_ifraem_id_ = $session->pull('_list_ifraem_id_', '');
        $_success_message_ = $session->pull('_success_message_', 'success');
        $_list_after_save_ = $session->pull('_list_after_save_', '');

        $script = <<<EOT
        var _ifraem_id_ = '{$_ifraem_id_}';
        var _pjax = '{$_pjax}';
        var _list_ifraem_id_ = '{$_list_ifraem_id_}';
        var _list_after_save_ = '{$_list_after_save_}';
        window.Pops = [];
        if (_list_ifraem_id_ && !_list_after_save_)
        {
            //获取你容器的
            var iframes = top.document.getElementsByTagName("iframe");
            for(var i in iframes)
            {
                //找到对应的iframe
                if (iframes[i].id == _list_ifraem_id_)
                {

                    //获取对应的对象
                    var openner = iframes[i].contentWindow;
                    //刷新
                    openner.$.pjax.reload('#pjax-container');

                    if (top.bind_urls =='new_tab')
                    {
                        //新iframe页面
                        var tab_id = getCurrentId();
                        if(tab_id)
                        {
                            //显示提示信息
                            top.toastr.success('{$_success_message_}');
                            //关闭当前页面
                            top.closeTabByPageId(tab_id.replace(/^iframe_/i, ''));
                            doStop();
                        }
                    }
                    else if (top.bind_urls =='popup')
                    {
                        //弹出窗口
                        var index = parent.layer.getFrameIndex(window.name);
                        if(index)
                        {
                            //弹出提示信息
                            top.toastr.success('{$_success_message_}');
                            //关闭页面
                            parent.layer.close(index);
                            doStop();
                        }
                    }

                    break;
                }
            }
            return;
        }

        //如果是表单
        if(_ifraem_id_ && $('form').length)
        {
            $('form').append('<input type="hidden" name="_ifraem_id_" value="' + _ifraem_id_ + '" />');
        }

        if(!_pjax)
        {
            $('body').addClass('iframe-content');

            //面包宵
            $('body').on('click', '.breadcrumb li a', function() {
                var url = $(this).attr('href');
                if (url == top.iframes_index) {
                    top.addTabs({
                        id: '_admin_dashboard',
                        title: top.home_title,
                        close: false,
                        url: url,
                        urlType: 'absolute',
                        icon: '<i class="fa ' + top.home_icon + '"></i>'
                    });
                    return false;
                }
            });

            if ((top.bind_urls =='new_tab' || top.bind_urls =='popup') && top.bind_selecter)
            {

                $('body').on('click', top.bind_selecter, function() {
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

                    var icon = '<i class="fa fa-file-text"></i>';
                    if ($(this).find('i.fa').length) {
                        icon = $(this).find('i.fa').prop("outerHTML");
                    }

                    var title = ($(this).text() || $(this).attr('title') || '').trim();

                    var tab_id = getCurrentId();

                    if(!tab_id)
                    {
                    // return true;
                    }

<!--                    url += (url.indexOf('?')>-1? '&':'?') + '_ifraem_id_=' + tab_id;-->

                    tab_id = tab_id.replace(/^iframe_(.+)$/ ,'$1');

                    var tab = top.findTabTitle(tab_id);

                    if (!tab)
                    {
                        //return true;
                    }

                    if(tab)
                    {
                        title = ' ' + tab.text() + (title ? '-' + title : '');
                    }

                    if(top.bind_urls == 'popup')
                    {
                        var area = false;
                        var popw = $(this).attr('popw');
                        var poph = $(this).attr('poph');
                        if(popw && poph)
                        {
                            area = [popw, poph];
                        }
                        openPop(url, icon + title, area);
                    }
                    else
                    {
                        top.openTab(url, title || '*', icon);
                    }

                    var toggle = false;
                    if ($(this).parents('.grid-dropdown-actions').length && (toggle = $(this).parents('.grid-dropdown-actions').find('.dropdown-toggle'))) {
                        toggle.trigger('click');
                    }

                    return false;
                });
            }

            window.getCurrentId = function()
            {
                var iframes = top.document.getElementsByTagName("iframe");
                for(var i in iframes)
                {
                    if (iframes[i].contentWindow == window)
                    {
                        return '' + iframes[i].id;
                    }
                }
                return '';
            }

            window.doStop = function()
            {
                if(!!(window.attachEvent && !window.opera)){
                    document.execCommand("stop");
                }
                else {
                    window.stop();
                }
            }

            window.openPop = function(url, title ,area) {
                if (!area) {
                    area = ['100%', '100%'];
                }
                var index = layer.open({
                    content: url,
                    type: 2,
                    title: title,
                    anim: 2,
                    closeBtn: 1,
                    shade: false,
                    area: area,
                });

                window.Pops.push(index);

                return index;
            }

            window.closePop = function()
            {
                var index = parent.layer.getFrameIndex(window.name);
                parent.layer.close(index);
            }

            window.openTab = function() {
            }

            window.closeTab = function()
            {
                var tab_id = getCurrentId();
                if(tab_id)
                {
                    top.closeTabByPageId(tab_id.replace(/^iframe_/i, ''));
                    doStop();
                }
            }
        }
EOT;
        Admin::script($script);
    }
}
