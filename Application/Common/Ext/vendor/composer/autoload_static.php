<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1c4e330ac861e25ca678b1a181de77ab
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Cocur\\BackgroundProcess\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Cocur\\BackgroundProcess\\' => 
        array (
            0 => __DIR__ . '/..' . '/cocur/background-process/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1c4e330ac861e25ca678b1a181de77ab::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1c4e330ac861e25ca678b1a181de77ab::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}