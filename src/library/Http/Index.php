<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Http;

use App\Psrphp\Admin\Http\Common;
use PsrPHP\Template\Template;

class Index extends Common
{
    public function get(
        Template $template
    ) {
        return $template->renderFromFile('index@ebcms/cloud');
    }
}
