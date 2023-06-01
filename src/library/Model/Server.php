<?php

declare(strict_types=1);

namespace App\Ebcms\Cloud\Model;

use Composer\InstalledVersions;
use Exception;
use PsrPHP\Router\Router;
use PsrPHP\Framework\Config;
use PsrPHP\Framework\Framework;
use Throwable;

class Server
{
    private $api;

    public function __construct(
        Config $config
    ) {
        $this->api = $config->get('api.host@ebcms/cloud', 'https://www.ebcms.com/plugin/cloud/api');
    }

    public function query(string $path, array $param = []): array
    {
        try {
            $url = $this->api . $path . '?' . http_build_query($this->getCommonParam());
            $response = $this->post($url, $param);
            $res = (array) json_decode($response, true);
            if (!isset($res['errcode'])) {
                return [
                    'errcode' => 1,
                    'message' => '错误：服务器无效响应！',
                ];
            }
            if ($res['errcode']) {
                $res['message'] = '服务器消息：' . ($res['message'] ?? '');
            }
            return $res;
        } catch (Throwable $th) {
            return [
                'errcode' => 1,
                'message' => '错误：' . $th->getMessage(),
            ];
        }
    }

    private function getCommonParam(): array
    {
        $package = InstalledVersions::getRootPackage();
        $res = [];
        $res['project'] = $package['name'];
        $res['version'] = $package['pretty_version'];
        $res['site'] = Framework::execute(function (
            Router $router
        ): string {
            return $router->build('/');
        });
        return $res;
    }

    private function post($url, array $data)
    {
        $data = http_build_query($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Length: ' . strlen($data),
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception($error);
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code >= 400) {
            $error = "HTTP error - $http_code";
            curl_close($ch);
            throw new Exception($error);
        }
        curl_close($ch);
        return $response;
    }
}
