<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Psrphp\Admin\Http\Common;
use App\Ebcms\Cloud\Model\Server;
use PsrPHP\Psr16\LocalAdapter;
use PsrPHP\Router\Router;
use PsrPHP\Session\Session;
use Throwable;

class Source extends Common
{
    public function get(
        Server $server,
        Router $router,
        LocalAdapter $cache,
        Session $session
    ) {
        try {
            $token = 'store_' . md5(uniqid() . rand(10000000, 99999999));
            $cache->set('cloudapitoken', $token, 30);
            $param = [
                'api' => $router->build('/ebcms/cloud/api', [
                    'token' => $token
                ]),
            ];
            $res = $server->query('/source', $param);
            if ($res['errcode']) {
                return $this->error($res['message'], $res['redirect_url'] ?? '', $res['errcode']);
            }
            if (null === $clouditem = $cache->get($token)) {
                return $this->error('超时，请重新操作~');
            }
            $session->set('clouditem', $clouditem);
            return $this->success($res['message']);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
