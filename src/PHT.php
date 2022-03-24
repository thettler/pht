<?php

namespace Thettler\Pht;

use Composer\Autoload\ClassLoader;
use function Composer\Autoload\includeFile;

class PHT
{
    public static function autoload(
        ClassLoader $loader,
        string $src = 'app',
        string $target = '.pht'
    ) {
        spl_autoload_register(function ($class) use ($src, $target, $loader) {
            if ($file = $loader->findFile($class)) {
                includeFile($file);

                return true;
            }

            $loaderRef = (new \ReflectionClass($loader));
            $method = $loaderRef->getMethod('findFileWithExtension');
            $method->setAccessible(true);

            if ($file = $method->invoke($loader, $class, '.pht')) {
                $file = str_replace($src, $target, $file);
                $file = rtrim($file, '.pht').'.php';
                includeFile($file);

                return true;
            }

            return null;
        }, true, true);
    }
}
