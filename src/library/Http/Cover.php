<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Psrphp\Admin\Http\Common;
use Composer\Autoload\ClassLoader;
use PsrPHP\Session\Session;
use Exception;
use ReflectionClass;
use Throwable;
use ZipArchive;

class Cover extends Common
{
    public function get(
        Session $session
    ) {
        try {
            $clouditem = $session->get('clouditem');
            $root = dirname(dirname(dirname((new ReflectionClass(ClassLoader::class))->getFileName())));
            $this->unZip($clouditem['tmpfile'], $root);
            return $this->success('文件更新成功!');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
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
