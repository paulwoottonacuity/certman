<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1730a06aed84c4a78c15e39beebfe096
{
    public static $files = array (
        'b2b4a332d57ae98e284061ea6e9cf11d' => __DIR__ . '/..' . '/analogic/lescript/Lescript.php',
    );

    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\CaBundle\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\CaBundle\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/ca-bundle/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1730a06aed84c4a78c15e39beebfe096::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1730a06aed84c4a78c15e39beebfe096::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
