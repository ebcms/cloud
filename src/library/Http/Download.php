<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Admin\Lib\Curl;
use PsrPHP\Session\Session;
use Throwable;

class Download extends Common
{

    public function get(
        Session $session,
        Curl $curl
    ) {
        try {
            $clouditem = $session->get('clouditem');
            if (false === $content = $curl->get($clouditem['source'])) {
                return $this->error('升级包下载失败，请稍后再试~');
            }

            if (md5($content) != $clouditem['md5']) {
                return $this->error('校验失败！');
            }

            $tmpfile = tempnam(sys_get_temp_dir(), 'clouditem');

            if (false === file_put_contents($tmpfile, $content)) {
                return $this->error('文件' . $tmpfile . '写入失败，请检查权限~');
            }
            $clouditem['tmpfile'] = $tmpfile;
            $session->set('clouditem', $clouditem);

            return $this->success('下载成功！');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
