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

        $items = [
            'header' => '',
            'trans' => [
                'oprations' => trans('iframe-tabs::iframe.oprations'),
                'refresh_current' => trans('iframe-tabs::iframe.refresh_current'),
                'close_current' => trans('iframe-tabs::iframe.close_current'),
                'close_all' => trans('iframe-tabs::iframe.close_all'),
                'close_other' => trans('iframe-tabs::iframe.close_other'),
                'open_in_new' => trans('iframe-tabs::iframe.open_in_new'),
                'open_in_pop' => trans('iframe-tabs::iframe.open_in_pop'),
                'scroll_left' => trans('iframe-tabs::iframe.scroll_left'),
                'scroll_right' => trans('iframe-tabs::iframe.scroll_right'),
                'scroll_current' => trans('iframe-tabs::iframe.scroll_current'),
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
            ->header('首页')
            ->description('首页')
            ->body("首页");
    }

}
