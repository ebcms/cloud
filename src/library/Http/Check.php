<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Cloud\Model\Server;
use Throwable;

class Check extends Common
{
    public function get(
        Server $server
    ) {
        try {
            $res = $server->query('/check');
            if ($res['errcode']) {
                return $this->error($res['message'], $res['redirect_url'] ?? null, $res['errcode'] ?? 1, $res['data'] ?? null);
            }
            return $this->success($res['message'], $res['data']);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
