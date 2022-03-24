<?php

namespace Thettler\Pht\Tests\testDump\src;

class PhtTestClass {
/**
* @template T
* @return T
*/
protected function testMethod(): mixed{
/** @var T $var */
$var = 'string';
        return $var;
    }
}