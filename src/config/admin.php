<?php

use App\Ebcms\Admin\Model\Account;
use App\Ebcms\Cloud\Http\Index;
use PsrPHP\Framework\Framework;
use PsrPHP\Router\Router;

return Framework::execute(function (
    Account $account,
    Router $router
): array {
    $menus = [];
    if ($account->checkAuth(Index::class)) {
        $menus[] = [
            'url' => $router->build('/ebcms/cloud/index'),
            'title' => '系统升级',
        ];
    }
    return [
        'menus' => $menus,
    ];
});
