<?php

namespace Thettler\Pht;

use Thettler\Pht\Contracts\Compiler;

class ClassCompiler implements Compiler
{
    public const REGEX = '/(?<complete>(?<abstract>abstract\s+)?class\s+(?<name>\w+)\s*(?:<?(?<generic>.+?)?>)?\s*(?:extends\s+(?<extends>[\w\\\\]+))?\s*(?:implements\s+(?<implements>[\w\\\\,\s]+?))?\s*{\s*(?<content>[\s\S]*))/m';

    public function compile(string $phtCode): string
    {
        preg_match_all(self::REGEX, $phtCode, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $phtCode = str_replace($match['complete'], $this->render($match), $phtCode);
        }

        return $phtCode;
    }

    protected function render(array $match): string
    {
        [
            'name' => $name,
            'generic' => $generic,
            'abstract' => $abstract,
            'extends' => $extends,
            'implements' => $implements,
            'content' => $content
        ] = $match;

        $docBlock = $this->renderDocBlock($generic);
        $generics = $this->extractGenerics($generic);

        $content = (new MethodCompiler($generics))->compile($content);
        $content = (new AttributeCompiler($generics))->compile($content);

        $classExIm = '';

        if ($extends) {
            $classExIm .= "extends {$extends} ";
        }


        if ($implements) {
            $classExIm .= "implements {$implements} ";
        }

        $class = <<<EOF
        class $name $classExIm{
        $content
        EOF;

        $class = $abstract . $class;

        if ($docBlock) {
            return $docBlock.$class;
        }

        return $class;
    }

    protected function renderDocBlock(string $generics): string
    {
        $docBlock = [];

        if ($generics) {
            foreach ($this->extractGenerics($generics) as $generic) {
                $docBlock[] = '* @template '.$generic;
            }
        }

        if (empty($docBlock)) {
            return '';
        }

        array_unshift($docBlock, '/**');
        $docBlock[] = '*/';

        return implode(PHP_EOL, $docBlock).PHP_EOL;
    }

    protected function extractGenerics($generics): array
    {
        return array_map(fn ($generic) => trim($generic), explode(',', $generics));
    }
}
