<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Psrphp\Admin\Http\Common;
use App\Psrphp\Admin\Lib\Curl;
use App\Psrphp\Admin\Lib\Response;
use PsrPHP\Session\Session;
use Throwable;

class Download extends Common
{

    public function get(
        Session $session
    ) {
        try {
            $clouditem = $session->get('clouditem');
            if (false === $content = Curl::get($clouditem['source'])) {
                return Response::error('升级包下载失败，请稍后再试~');
            }

            if (md5($content) != $clouditem['md5']) {
                return Response::error('校验失败！');
            }

            $tmpfile = tempnam(sys_get_temp_dir(), 'clouditem');

            if (false === file_put_contents($tmpfile, $content)) {
                return Response::error('文件' . $tmpfile . '写入失败，请检查权限~');
            }
            $clouditem['tmpfile'] = $tmpfile;
            $session->set('clouditem', $clouditem);

            return Response::success('下载成功！');
        } catch (Throwable $th) {
            return Response::error($th->getMessage());
        }
    }
}
