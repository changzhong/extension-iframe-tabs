dcat-admin iframe多窗口扩展
======

* dcatadmin版本需要1.7.0以上

1.安装扩展
```
composer require changzhong/extension-iframe-tabs
```

2.在后台扩展打开扩展，并导入

3.在config/admin-extensions.php添加配置
```
    'iframe-tabs' => [
        'enable' => true,
        'home_action' => 'App\Admin\Controllers\HomeController@home',
        'home_title' => 'Home',
        'home_icon' => 'fa-home',
        'use_icon' => true,
        'tabs_css' => 'vendor/laravel-admin-ext/iframe-tabs/dashboard.css',
        'layer_path' => 'vendor/laravel-admin-ext/iframe-tabs/layer/layer.js',
        'pass_urls' => [
            0 => '/auth/logout',
            1 => '/auth/lock',
        ],
        'force_login_in_top' => true,
        'tabs_left' => 42,
        'bind_urls' => 'popup',
        'bind_selecter' => 'a.grid-row-view,a.grid-row-edit,.column-__actions__ ul.dropdown-menu a,.box-header .pull-right .btn-success,.popup',
    ],
```

5.清除缓存
```shell script
php artisan cache:clear;
php artisan view:clear;
```
6.打开链接 你的域名/admin即可


给a标签添加class=pop-link为弹窗打开，添加 class=iframe-link为新添加iframe窗口




