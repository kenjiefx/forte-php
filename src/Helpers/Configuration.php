<?php

namespace Kenjiefx\Forte\Helpers;

use Kenjiefx\Forte\Exceptions\NotFoundException;

class Configuration
{
    public static $forte_composer_path = ROOT.'/forte.composer.json';
    public static $my_forte_path       = ROOT.'/my.forte.json';
    public static $composer_path       = ROOT.'/composer.json';
    private static array $configuration;

    public static function init()
    {
        if (!isset(static::$configuration)) {

            if (!file_exists(Self::$forte_composer_path)||!file_exists(Self::$my_forte_path)) {
                throw new NotFoundException('Error::missing files/configuration');
            }

            if (!file_exists(Self::$composer_path)) {
                throw new NotFoundException('Error::missing composer.json');
            }
    
            $my = json_decode(file_get_contents(Self::$my_forte_path),TRUE);
    
            $configuration = [
                'username' => $my['username'],
                'token'    => $my['token'],
                'composer' => json_decode(file_get_contents(Self::$composer_path),TRUE),
                'requires' => json_decode(file_get_contents(Self::$forte_composer_path),TRUE)
            ];

            static::$configuration = $configuration;

            return $configuration;

        }

        return static::$configuration;

    }
}
