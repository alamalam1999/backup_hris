<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb2d8ad767c8bbab3927e632f19ad8d3d
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'setasign\\Fpdi\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'setasign\\Fpdi\\' => 
        array (
            0 => __DIR__ . '/..' . '/setasign/fpdi/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb2d8ad767c8bbab3927e632f19ad8d3d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb2d8ad767c8bbab3927e632f19ad8d3d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
