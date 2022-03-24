<?php

namespace Thettler\Pht;

use Thettler\Pht\Contracts\Compiler;

class AttributeCompiler implements Compiler
{
    public function __construct(protected array $parentGenerics = [])
    {
    }

    public function compile(string $phtCode): string
    {
        preg_match_all(
            '/(?:(?<visibility>pub|private)\s+)?(?:(?<access>static|readonly)\s+)?(?<name>\$\w+)\s*:\s*(?<type>[^=|^\s]+)*\s*=?\s*(?<value>[^;|^\s]+)*\s*;/m',
            $phtCode,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $phtCode = str_replace($match[0], $this->render($match), $phtCode);
        }

        return $phtCode;
    }

    protected function phtTypeToPhpStan(string $type): string
    {
        $str = lcfirst($type);
        $str = preg_replace("/[A-Z]/", '-'."$0", $str);

        return strtolower($str);
    }

    protected function render(array $match): string
    {
        [
            'visibility' => $visibility,
            'access' => $access,
            'name' => $name,
            'type' => $type,
        ]
            = $match;


        $phpType = Types::toPhpType($type);
        $type = Types::phtToStan($type);

        if (in_array($phpType, $this->parentGenerics)) {
            $phpType = 'mixed';
        }

        $visibility = $this->extractVisibility($visibility);
        $value = isset($match['value']) ? '= '.$match['value'].';' : ';';

        return <<<EOF
/** @var $type */
$visibility $access $phpType $name $value
EOF;
    }

    protected function extractVisibility(string $visibility): string
    {
        return match ($visibility) {
            'pub' => 'public',
            'private' => 'private',
            default => 'protected',
        };
    }
}
