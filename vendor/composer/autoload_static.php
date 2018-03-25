<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita0593da88c6481ccaa0dd65171b7b72a
{
    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'OrganizeSeries\\MultiplesAddon\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'OrganizeSeries\\MultiplesAddon\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 
            array (
                0 => __DIR__ . '/..' . '/composer/installers/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita0593da88c6481ccaa0dd65171b7b72a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita0593da88c6481ccaa0dd65171b7b72a::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInita0593da88c6481ccaa0dd65171b7b72a::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}