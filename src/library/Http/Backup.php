<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Admin\Lib\Zip;
use Composer\Autoload\ClassLoader;
use PsrPHP\Session\Session;
use ReflectionClass;
use Throwable;
use ZipArchive;

class Backup extends Common
{

    public function get(
        Session $session,
        Zip $zip
    ) {
        try {
            $root = dirname(dirname(dirname((new ReflectionClass(ClassLoader::class))->getFileName())));
            $clouditem = $session->get('clouditem');
            $clouditem['backup_file'] = $root . '/backup/system_' . date('YmdHis') . '.zip';

            $zip->open($clouditem['backup_file'], ZipArchive::CREATE);
            $zip->addDirectory($root . '/vendor', $root . '/');
            $zip->addFile($root . '/composer.json', 'composer.json');
            $zip->addFile($root . '/composer.lock', 'composer.lock');
            $zip->close();

            $session->set('clouditem', $clouditem);
            return $this->success('备份成功！', $clouditem);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
