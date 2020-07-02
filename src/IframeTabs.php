<?php

namespace Dcat\Admin\Extension\IframeTabs;

use Dcat\Admin\Admin;
use Dcat\Admin\Extension;
use Illuminate\Console\Command;

class IframeTabs extends Extension
{
    const NAME = 'iframe-tabs';

    protected $serviceProvider = IframeTabsServiceProvider::class;

    protected $composer = __DIR__.'/../composer.json';

    protected $assets = __DIR__.'/../resources/assets';

    protected $views = __DIR__.'/../resources/views';

//    protected $lang = __DIR__.'/../resources/lang';

    protected $menu = [
        'title' => 'Iframetabs',
        'path'  => 'iframe-tabs',
        'icon'  => '',
    ];


    public static $manifestData = [];

    /**
     * {@inheritdoc}
     */
    public function import(Command $command)
    {
//        if ($menu = Menu::where('uri', '/')->first()) {
//            $menu->update(['uri' => 'dashboard']);
//        }
//        if (!Permission::where('slug', 'tabs.dashboard')->first()) {
//            parent::createPermission('Tab-dashboard', 'tabs.dashboard', 'dashboard');
//        }
    }

    public static function fixMinify()
    {
        if (!static::isMinify()) {
            return;
        }
        Admin::$baseJs = Admin::$baseCss = Admin::$css =  Admin::$js = [];

        Admin::js(static::getManifestData('js'));
        Admin::css(static::getManifestData('css'));
    }

    public static function isMinify()
    {
        return false;
        if (!isset(Admin::$manifest)) {
            return false;
        }

        if (!config('admin.minify_assets') || !file_exists(public_path(Admin::$manifest))) {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function getManifestData($key)
    {
        if (!empty(static::$manifestData)) {
            return static::$manifestData[$key];
        }

        static::$manifestData = json_decode(
            file_get_contents(public_path(Admin::$manifest)),
            true
        );

        return static::$manifestData[$key];
    }
}
