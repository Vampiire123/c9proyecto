<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6f0afe89ded0571f132cb4f172cfd04e
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6f0afe89ded0571f132cb4f172cfd04e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6f0afe89ded0571f132cb4f172cfd04e::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
