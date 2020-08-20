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

    protected $lang = __DIR__.'/../resources/lang';
}
