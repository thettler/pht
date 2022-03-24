<?php

namespace Thettler\Pht;

class MethodCompiler extends FunctionCompiler
{
    public const REGEX = '/(?<complete>(?:(?<visibility>pub|private)\s+)?(?:(?<static>static)\s+)?fn\s+(?<name>\w+)\s*<?(?<generic>.+?)?>?\((?<params>\w+.*)?\)\s*:?(?(?<=:)(?:\s*(?<return>.+?(?=[{|\s]))))\s*{\s*(?<content>[\s\S]*?)\s*})/m';

    public function compile(string $phtCode): string
    {
        preg_match_all(self::REGEX, $phtCode, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            $phtCode = str_replace($match['complete'], $this->render($match), $phtCode);
        }

        return $phtCode;
    }

    protected function render(array $match): string
    {
        [
            'name' => $name,
            'visibility' => $visibility,
            'static' => $static,
            'generic' => $generic,
            'params' => $params,
            'return' => $return,
            'content' => $content,
        ] = $match;

        $functionPrefix = [];
        $docBlock = $this->renderDocBlock($generic, $params, $return);
        $generics = $this->extractGenerics($generic);
        $functionPrefix[] = $this->extractVisibility($visibility, $name);
        if ($static) {
            $functionPrefix[] = 'static';
        }
        $parameterSignature = $this->renderParameterSignature($params, $generics);
        $return = $this->renderReturn($return, $name, $generics);
        $functionPrefix = implode(' ', $functionPrefix);
        $content = (new VariableCompiler())->compile($content);

        $function = <<<EOF
        $functionPrefix function $name($parameterSignature)$return{
        $content
            }
        EOF;

        if ($docBlock) {
            return $docBlock.$function;
        }

        return $function;
    }

    protected function extractVisibility(string $visibility, string $name): string
    {
        return match ($visibility) {
            'pub' => 'public',
            'private' => 'private',
            default => ($name === '__construct') ? 'public' : 'protected',
        };
    }
}
