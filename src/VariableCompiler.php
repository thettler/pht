<?php

namespace Thettler\Pht;

use Thettler\Pht\Contracts\Compiler;

class VariableCompiler implements Compiler
{
    public function compile(string $phtCode): string
    {
        preg_match_all('/(\$\w+)\s*:\s*([^=|^\s]+)*\s*([=|+|-|?]+)\s*([^;|^\s]+)*\s*;/m', $phtCode, $matches, PREG_SET_ORDER);

        foreach ($matches as [$match, $var, $type, $operator, $expression]) {
            $type = Types::phtToStan($type);
            $phtCode = str_replace($match, $this->render($type, $var, $operator, $expression), $phtCode);
        }

        return $phtCode;
    }

    protected function phtTypeToPhpStan(string $type): string
    {
        $str = lcfirst($type);
        $str = preg_replace("/[A-Z]/", '-'."$0", $str);

        return strtolower($str);
    }

    protected function render(string $type, string $varname, string $operator, string $expression): string
    {
        return <<<EOF
/** @var $type $varname */
$varname $operator $expression;
EOF;
    }
}
