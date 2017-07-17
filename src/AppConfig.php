<?php

namespace ZanPHP\Config;

use ZanPHP\Support\Arr;

class AppConfig
{
    private static $configMap = [];

    public static function init()
    {
        $runMode = getenv("runMode");
        $path = getenv("path.app");
        if(is_dir($path)) {
            $sharePath = $path . 'share/';
            $shareConfigMap = ConfigLoader::getInstance()->load($sharePath);
            $config = getenv("path.config");
            $runModeConfigPath = $config . $runMode;
            $runModeConfig = ConfigLoader::getInstance()->load($runModeConfigPath);
            self::$configMap = Arr::merge(self::$configMap, $shareConfigMap, $runModeConfig);
        }
    }

    public static function get($key, $default = null)
    {
        $preKey = $key;
        $routes = explode('.', $key);
        if (empty($routes)) {
            return $default;
        }

        $result = &self::$configMap;
        $hasConfig = true;
        foreach ($routes as $route) {
            if (!isset($result[$route])) {
                $hasConfig = false;
                break;
            }
            $result = &$result[$route];
        }
        if (!$hasConfig) {
            return IronConfig::get($preKey, $default);
        }
        return $result;
    }

    public static function set($key, $value)
    {
        $routes = explode('.', $key);
        if (empty($routes)) {
            return false;
        }

        $newConfigMap = Arr::createTreeByList($routes, $value);
        self::$configMap = Arr::merge(self::$configMap, $newConfigMap);

        return true;
    }

    public static function clear()
    {
        self::$configMap = [];
    }
}
