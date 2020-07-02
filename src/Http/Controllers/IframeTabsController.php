<?php

namespace Dcat\Admin\Extension\IframeTabs\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Extension\IframeTabs\IframeTabs;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Navbar;
use Illuminate\Routing\Controller;

class IframeTabsController extends Controller
{
    public function index(Content $content)
    {
        $iframeTabs = new IframeTabs();
//        if (!$iframeTabs->boot()) {
//            return redirect(admin_base_path('dashboard'));
//        }

        $items = [
            'header' => '',
            'trans' => [
                'oprations' => trans('admin.iframe_tabs.oprations'),
                'refresh_current' => trans('admin.iframe_tabs.refresh_current'),
                'close_current' => trans('admin.iframe_tabs.close_current'),
                'close_all' => trans('admin.iframe_tabs.close_all'),
                'close_other' => trans('admin.iframe_tabs.close_other'),
                'open_in_new' => trans('admin.iframe_tabs.open_in_new'),
                'open_in_pop' => trans('admin.iframe_tabs.open_in_pop'),
                'scroll_left' => trans('admin.iframe_tabs.scroll_left'),
                'scroll_right' => trans('admin.iframe_tabs.scroll_right'),
                'scroll_current' => trans('admin.iframe_tabs.scroll_current'),
                'refresh_succeeded' => trans('admin.refresh_succeeded'),
            ],
            'home_uri' => admin_base_path('dashboard'),
            'home_title' => $iframeTabs->config('home_title', 'Index'),
            'home_icon' => $iframeTabs->config('home_icon', 'fa-home'),
            'use_icon' => $iframeTabs->config('use_icon', true) ? '1' : '',
            'pass_urls' => implode(',', $iframeTabs->config('pass_urls', ['/auth/logout'])),
            'iframes_index' => admin_url(),
            'tabs_left' => $iframeTabs->config('tabs_left', '42'),
            'bind_urls' => $iframeTabs->config('bind_urls', 'none'),
            'bind_selecter' => $iframeTabs->config('bind_selecter', '.box-body table.table tbody a.grid-row-view,.box-body table.table tbody a.grid-row-edit,.box-header .pull-right .btn-success'),
        ];

        \View::share($items);

        Admin::navbar(function (Navbar $navbar) {
            $navbar->left(view('iframe-tabs::ext.tabs'));
            $navbar->right(view('iframe-tabs::ext.options'));
        });

        return $content;
    }

    public function dashboard(Content $content)
    {
        return $content
            ->header('Defautl page')
            ->description('Defautl page')
            ->body('Defautl page have not seted ,pleace edit config in `config/admin.php`'
                . "<pre>'extensions' => [
                'iframe-tabs' => [
                     // Set to `false` if you want to disable this extension
                    'enable' => true,
                    // The controller and action of dashboard page `/admin/dashboard`
                    'home_action' => App\Admin\Controllers\HomeController::class . '@index',//edit here
                    // Default page tab-title
                    'home_title' => 'Home',
                    // Default page tab-title icon
                    'home_icon' => 'fa-home',
                    // wheath show icon befor titles for all tab
                    'use_icon' => true,
                    // dashboard css
                    'tabs_css' =>'vendor/laravel-admin-ext/iframe-tabs/dashboard.css',
                    // layer.js path , if you do not user laravel-admin-ext\cropper , set another one
                    'layer_path' => 'vendor/laravel-admin-ext/cropper/layer/layer.js',
                    // href links do not open in tab .
                    'pass_urls' => ['/admin/auth/logout', '/admin/auth/lock'],
                    // When login session state of a tab-page was expired , force top-level window goto login page .
                    'force_login_in_top' => true,
                    // tabs left offset
                    'tabs_left'  => 42,
                    // bind click event of table actions [edit / view]
                    'bind_urls' => 'popup', //[ popup / new_tab / none]
                    //table actions dom selecter
                    'bind_selecter' => '.box-body table.table tbody a.grid-row-view,.box-body table.table tbody a.grid-row-edit,.box-header .pull-right .btn-success,.popup',
                    //.box-body table.table tr>td a,.box-header .pull-right .btn-success
                ]
            ],</pre>");
    }
}
