<?php

namespace Thettler\Pht;

use Thettler\Pht\Contracts\Compiler;

class VariableCompiler implements Compiler
{
    public const REGEX = '/(\$\w+)\s*:\s*([^=|^\s]+)*\s*([=|+|-|?]+)\s*([^;|^\s]+)*\s*;/m';

    public function compile(string $phtCode): string
    {
        preg_match_all(static::REGEX, $phtCode, $matches, PREG_SET_ORDER);

        foreach ($matches as [$match, $var, $type, $operator, $expression]) {
            $type = Types::phtToStan($type);
            $phtCode = str_replace($match, $this->render($type, $var, $operator, $expression), $phtCode);
        }

        return $phtCode;
    }

    protected function render(string $type, string $varname, string $operator, string $expression): string
    {
        return <<<EOF
/** @var $type $varname */
$varname $operator $expression;
EOF;
    }
}
