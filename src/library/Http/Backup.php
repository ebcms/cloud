<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Psrphp\Admin\Http\Common;
use App\Psrphp\Admin\Lib\Response;
use App\Psrphp\Admin\Lib\Zip;
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
            if (is_dir($root . '/vendor')) {
                $zip->addDirectory($root . '/vendor', $root . '/');
            }
            $zip->addFile($root . '/composer.json', 'composer.json');
            $zip->addFile($root . '/composer.lock', 'composer.lock');
            $zip->close();

            $session->set('clouditem', $clouditem);
            return Response::success('备份成功！', $clouditem);
        } catch (Throwable $th) {
            return Response::error($th->getMessage());
        }
    }
}
