<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Psrphp\Admin\Http\Common;
use App\Ebcms\Cloud\Model\Server;
use App\Psrphp\Admin\Lib\Response;
use Throwable;

class Check extends Common
{
    public function get(
        Server $server
    ) {
        try {
            $res = $server->query('/check');
            if ($res['errcode']) {
                return Response::error($res['message'], $res['redirect_url'] ?? null, $res['errcode'] ?? 1, $res['data'] ?? null);
            }
            return Response::success($res['message'], $res['data']);
        } catch (Throwable $th) {
            return Response::error($th->getMessage());
        }
    }
}
