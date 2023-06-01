<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Ebcms\Admin\Http\Common;
use Composer\Autoload\ClassLoader;
use PsrPHP\Session\Session;
use ReflectionClass;

use function Composer\Autoload\includeFile;
use Throwable;

class Install extends Common
{
    public function get(
        Session $session
    ) {
        try {
            $clouditem = $session->get('clouditem');
            $root = dirname(dirname(dirname((new ReflectionClass(ClassLoader::class))->getFileName())));
            $upgrade_file = $root . '/upgrade.php';
            if (file_exists($upgrade_file)) {
                includeFile($upgrade_file);
            }

            if (file_exists($upgrade_file)) {
                unlink($upgrade_file);
            }

            if (file_exists($clouditem['tmpfile'])) {
                unlink($clouditem['tmpfile']);
            }

            $session->delete('clouditem');

            return $this->success('升级成功!');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }
}
