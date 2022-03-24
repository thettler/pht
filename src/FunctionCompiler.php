<?php

namespace Thettler\Pht;

use Thettler\Pht\Contracts\Compiler;

class FunctionCompiler implements Compiler
{
    public const REGEX = '/(?<complete>fn\s+(?<name>\w+)\s*<?(?<generic>.+?)?>?\((?<params>\w+.*)?\)\s*:?(?(?<=:)(?:\s*(?<return>.+?(?=[{|\s]))))\s*{\s*(?<content>[\s\S]*?)\s*})/m';

    public function __construct(protected array $parentGenerics = [])
    {
    }

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
            'generic' => $generic,
            'params' => $params,
            'return' => $return,
            'content' => $content,
        ] = $match;
        $docBlock = $this->renderDocBlock($generic, $params, $return);
        $generics = $this->extractGenerics($generic);
        $parameterSignature = $this->renderParameterSignature($params, $generics);
        $return = $this->renderReturn($return, $name, $generics);

        $function = <<<EOF
        function $name($parameterSignature)$return{
        $content
        }
        EOF;

        if ($docBlock) {
            return $docBlock.$function;
        }

        return $function;
    }

    protected function renderParameterSignature(string $params, array $generics = []): string
    {
        if (! $params) {
            return '';
        }

        $params = array_map(
            function (array $param) use ($generics) {
                $type = Types::toPhpType($param['type']);

                if (in_array($type, [...$this->parentGenerics, ...$generics])) {
                    return 'mixed'.' '.$param['identifier'];
                }

                return $type.' '.$param['identifier'];
            },
            $this->extractParams($params)
        );

        return implode(', ', $params);
    }

    protected function renderDocBlock(string $generics, $params, string $return): string
    {
        $docBlock = [];

        if ($generics) {
            foreach ($this->extractGenerics($generics) as $generic) {
                $docBlock[] = '* @template '.$generic;
            }
        }

        if ($params) {
            foreach ($this->extractParams($params) as $param) {
                $docBlock[] = '* @param '.Types::phtToStan($param['type']).' '.$param['identifier'];
            }
        }

        if ($return) {
            $docBlock[] = '* @return '.Types::phtToStan($return);
        }

        if (empty($docBlock)) {
            return '';
        }

        array_unshift($docBlock, '/**');
        $docBlock[] = '*/';

        return implode(PHP_EOL, $docBlock).PHP_EOL;
    }

    protected function extractParams(string $params): array
    {
        return array_map(function ($param) {
            [$type, $name] = explode('$', $param);

            return ['type' => trim($type), 'identifier' => '$'.trim($name)];
        }, explode(',', $params));
    }

    protected function extractGenerics($generics): array
    {
        return array_map(fn ($generic) => trim($generic), explode(',', $generics));
    }

    /**
     * @param  string  $return
     * @return string
     */
    protected function renderReturn(string $return, string $name, array $generics = []): string
    {
        if ($name === '__construct') {
            return '';
        }

        if ($return === '') {
            return ': void';
        }

        $type = Types::toPhpType($return);

        if (in_array($type, [...$this->parentGenerics,...$generics])) {
            return ': mixed';
        }

        return ': '.$type;
    }
}
