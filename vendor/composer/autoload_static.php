<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit541baa9433923f6ada57a42a70cfe0c1
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Bluefield\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Bluefield\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit541baa9433923f6ada57a42a70cfe0c1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit541baa9433923f6ada57a42a70cfe0c1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit541baa9433923f6ada57a42a70cfe0c1::$classMap;

        }, null, ClassLoader::class);
    }
}
