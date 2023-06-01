<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Ebcms\Admin\Http\Common;
use App\Ebcms\Admin\Traits\DirTrait;
use Composer\Autoload\ClassLoader;
use Exception;
use PsrPHP\Session\Session;
use ReflectionClass;
use Throwable;
use ZipArchive;

class Rollback extends Common
{
    use DirTrait;

    public function get(
        Session $session
    ) {
        try {
            $clouditem = $session->get('clouditem');
            $root = dirname(dirname(dirname((new ReflectionClass(ClassLoader::class))->getFileName())));
            $this->unZip($clouditem['backup_file'], $root);
        } catch (Throwable $th) {
            return $this->error('还原失败：' . $th->getMessage());
        }
    }

    private function unZip($file, $destination)
    {
        $zip = new ZipArchive();
        if ($zip->open($file) !== true) {
            throw new Exception('Could not open archive');
        }
        if (true !== $zip->extractTo($destination)) {
            throw new Exception('Could not extractTo ' . $destination);
        }
        if (true !== $zip->close()) {
            throw new Exception('Could not close archive ' . $file);
        }
    }
}