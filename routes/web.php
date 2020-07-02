<?php

use Dcat\Admin\Controllers\AuthController;
use Dcat\Admin\Extension\IframeTabs\Http\Controllers;
use Dcat\Admin\Extension\IframeTabs\Http\Controllers\IframeTabsController;

$iframeTabs = new Dcat\Admin\Extension\IframeTabs\IframeTabs();
Route::get('iframe-tabs', Controllers\IframeTabsController::class.'@index')->name('iframes.index');

Route::get('/', IframeTabsController::class . '@index')->name('iframes.index');

Route::get('/dashboard', config('admin-extensions.iframe-tabs.home_action', IframeTabsController::class . '@dashboard'))->name('iframes.dashboard');

if ($iframeTabs->config('force_login_in_top', true)) {

    $middleware = config('admin.route.middleware', []);

    array_push($middleware, 'iframe.login');

    $authController = config('admin.auth.controller', AuthController::class);

    Route::get('auth/login', $authController . '@getLogin')->middleware($middleware);
}
