<?php

namespace Thettler\Pht\Contracts;

interface Compiler
{
    public function compile(string $phtCode): string;
}
