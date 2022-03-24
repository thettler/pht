<?php

namespace Thettler\Pht;

use Thettler\Pht\Contracts\Compiler;

class PhtCompiler implements Compiler
{
    public function compile(string $phtCode): string
    {
        $phtCode = trim($phtCode);
        $phtCode = (new ClassCompiler())->compile($phtCode);
        $phtCode = (new FunctionCompiler())->compile($phtCode);
        $phtCode = (new VariableCompiler())->compile($phtCode);

        return $phtCode;
    }
}
